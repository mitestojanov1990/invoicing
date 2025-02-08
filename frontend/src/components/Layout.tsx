// src/components/Layout.tsx
import React from 'react';
import { Layout as AntLayout, Menu, Button } from 'antd';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import LanguageSwitcher from './LanguageSwitcher';
import { useAuth } from '../contexts/AuthContext';
import axios from 'axios';

const { Header, Content } = AntLayout;

const Layout: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const { t } = useTranslation();
  const location = useLocation();
  const navigate = useNavigate();
  const { user, setUser } = useAuth();
  const selectedKeys = [location.pathname];

  const handleSignOut = async () => {
    try {
      await axios.get('/logout');
      setUser(null);
      navigate('/signin');
    } catch (error) {
      console.error(error);
    }
  };

  return (
    <AntLayout className='min-h-screen'>
      <Header className='flex items-center justify-between'>
        <div className='flex items-center'>
          <div className='text-white font-bold text-xl mr-8'>
            <Link to='/invoices'>{t('layout.title', 'Modern Invoicing')}</Link>
          </div>
          <Menu
            theme='dark'
            mode='horizontal'
            selectedKeys={selectedKeys}
            items={[
              {
                key: '/invoices',
                label: (
                  <Link to='/invoices'>
                    {t('layout.allInvoices', 'All Invoices')}
                  </Link>
                ),
              },
              {
                key: '/invoices/create',
                label: (
                  <Link to='/invoices/create'>
                    {t('layout.createInvoice', 'Create Invoice')}
                  </Link>
                ),
              },
            ]}
            className='flex-1'
          />
        </div>
        <div className='flex items-center space-x-4'>
          {user ? (
            <Button onClick={handleSignOut}>
              {t('layout.signOut', 'Sign Out')}
            </Button>
          ) : (
            <Link to='/signin'>
              <Button type='primary'>{t('layout.signIn', 'Sign In')}</Button>
            </Link>
          )}
          <LanguageSwitcher />
        </div>
      </Header>
      <Content className='p-6'>{children}</Content>
    </AntLayout>
  );
};

export default Layout;
