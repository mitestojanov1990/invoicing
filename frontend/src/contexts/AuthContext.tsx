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
  const [loading, setLoading] = useState<boolean>(true); // Track loading state

  useEffect(() => {
    const token = localStorage.getItem('authToken');
    if (token) {
      try {
        const decoded = jwtDecode<{ exp?: number }>(token);

        // If token is expired, log the user out
        if (decoded.exp && decoded.exp * 1000 < Date.now()) {
          localStorage.removeItem('authToken');
          setUser(null);
          setLoading(false);
          return;
        }

        axios
          .get('/api/auth/me', {
            headers: { Authorization: `Bearer ${token}` },
            withCredentials: true, // Ensure session cookies are sent
          })
          .then((response) => {
            setUser(response.data.user);
          })
          .catch(() => {
            localStorage.removeItem('authToken');
            setUser(null);
          })
          .finally(() => setLoading(false));
      } catch (error) {
        console.error('Invalid token:', error);
        localStorage.removeItem('authToken');
        setUser(null);
        setLoading(false);
      }
    } else {
      setLoading(false);
    }
  }, []);

  const signIn = async (email: string, password: string): Promise<User> => {
    try {
      const response = await axios.post(
        '/api/auth/signin',
        { email, password },
        { withCredentials: true }
      );
      const token = response.data.token;
      localStorage.setItem('authToken', token);
      setUser(response.data.user);
      return response.data.user;
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
      const response = await axios.post(
        '/api/auth/signup',
        { name, email, password },
        { withCredentials: true }
      );
      const token = response.data.token;
      localStorage.setItem('authToken', token);
      setUser(response.data.user);
      return response.data.user;
    } catch (error) {
      console.error('Sign-up failed:', error);
      throw new Error('Error creating account');
    }
  };

  const signOut = async (): Promise<void> => {
    try {
      await axios.post('/api/auth/signout', {}, { withCredentials: true });
    } catch (error) {
      console.error('Sign out failed:', error);
    }
    localStorage.removeItem('authToken');
    setUser(null);
  };

  if (loading) {
    return <div>Loading...</div>; // Prevent rendering components before auth state is resolved
  }

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
