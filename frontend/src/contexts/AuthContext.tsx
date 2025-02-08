// src/contexts/AuthContext.tsx
import React, { createContext, useContext, useEffect, useState } from 'react';
import { User } from '../interfaces';
import { AuthContextType } from '../interfaces/AuthContextType';
import axios from 'axios';

const AuthContext = createContext<AuthContextType>({
  user: null,
  setUser: () => {},
});

export const AuthProvider: React.FC<{ children: React.ReactNode }> = ({
  children,
}) => {
  const [user, setUser] = useState<User | null>(null);

  useEffect(() => {
    // Attempt to fetch the current user session
    axios
      .get('/api/auth/me')
      .then((response) => {
        setUser(response.data.user);
      })
      .catch((error) => {
        console.log('User not authenticated', error);
      });
  }, []);

  return (
    <AuthContext.Provider value={{ user, setUser }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => useContext(AuthContext);
