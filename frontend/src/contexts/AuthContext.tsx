// src/contexts/AuthContext.tsx
import React, {
  createContext,
  useContext,
  useEffect,
  useState,
  ReactNode,
} from 'react';
import axios from 'axios';
import { jwtDecode } from 'jwt-decode';
import { User } from '../interfaces';
import { AuthContextType } from '../interfaces/AuthContextType';

const AuthContext = createContext<AuthContextType | undefined>(undefined);

export const AuthProvider: React.FC<{ children: ReactNode }> = ({
  children,
}) => {
  const [user, setUser] = useState<User | null>(null);

  useEffect(() => {
    const token = localStorage.getItem('authToken');
    if (token) {
      try {
        const decoded = jwtDecode(token);
        if (decoded.exp && decoded.exp * 1000 < Date.now()) {
          localStorage.removeItem('authToken'); // Expired token, logout user
          setUser(null);
          return;
        }

        axios
          .get('/api/auth/me', {
            headers: { Authorization: `Bearer ${token}` },
          })
          .then((response) => setUser(response.data.user))
          .catch(() => {
            localStorage.removeItem('authToken');
            setUser(null);
          });
      } catch (error) {
        console.error('Invalid token:', error);
        localStorage.removeItem('authToken');
        setUser(null);
      }
    }
  }, []);

  const signIn = async (email: string, password: string): Promise<User> => {
    try {
      const response = await axios.post('/api/auth/signin', {
        email,
        password,
      });
      const token = response.data.token;
      localStorage.setItem('authToken', token);

      const user = response.data.user;
      setUser(user);
      return user;
    } catch (error) {
      console.error('Login failed:', error);
      throw new Error('Invalid login credentials');
    }
  };

  const signUp = async (
    name: string,
    email: string,
    password: string
  ): Promise<User> => {
    try {
      const response = await axios.post('/api/auth/signup', {
        name,
        email,
        password,
      });
      const token = response.data.token;
      localStorage.setItem('authToken', token);

      const user = response.data.user;
      setUser(user);
      return user;
    } catch (error) {
      console.error('Sign-up failed:', error);
      throw new Error('Error creating account');
    }
  };

  const signOut = async (): Promise<void> => {
    localStorage.removeItem('authToken');
    delete axios.defaults.headers.common['Authorization'];
    setUser(null);
  };

  return (
    <AuthContext.Provider value={{ user, setUser, signIn, signUp, signOut }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = (): AuthContextType => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};
