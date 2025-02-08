// src/contexts/AuthContext.tsx
import React, {
  createContext,
  useContext,
  useEffect,
  useState,
  ReactNode,
} from 'react';
import axios from 'axios';
import { User } from '../interfaces';

interface AuthContextType {
  user: User | null;
  setUser: (user: User | null) => void;
  signIn: (email: string, password: string) => Promise<User>;
  signUp: (name: string, email: string, password: string) => Promise<User>;
  signOut: () => Promise<void>;
}

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider: React.FC<{ children: ReactNode }> = ({
  children,
}) => {
  const [user, setUser] = useState<User | null>(null);

  // Fetch the authenticated user on app load
  useEffect(() => {
    axios
      .get('/api/auth/me')
      .then((response) => setUser(response.data.user))
      .catch(() => setUser(null)); // If not authenticated, reset user state
  }, []);

  // Sign in method
  const signIn = async (email: string, password: string): Promise<User> => {
    const response = await axios.post<{ user: User }>('/api/auth/signin', {
      email,
      password,
    });
    setUser(response.data.user);
    return response.data.user;
  };

  // Sign up method
  const signUp = async (
    name: string,
    email: string,
    password: string
  ): Promise<User> => {
    const response = await axios.post<{ user: User }>('/api/auth/signup', {
      name,
      email,
      password,
    });
    setUser(response.data.user);
    return response.data.user;
  };

  // Sign out method
  const signOut = async (): Promise<void> => {
    await axios.post('/api/auth/signout');
    setUser(null);
  };

  return (
    <AuthContext.Provider value={{ user, setUser, signIn, signUp, signOut }}>
      {children}
    </AuthContext.Provider>
  );
};

// Custom hook for consuming auth context
export const useAuth = (): AuthContextType => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};
