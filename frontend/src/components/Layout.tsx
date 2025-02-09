// src/components/Layout.tsx
import React, { useState } from 'react';
import { Layout as AntLayout, Menu, Button, Drawer } from 'antd';
import { MenuOutlined } from '@ant-design/icons';
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

  const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

  const handleSignOut = async () => {
    try {
      await axios.get('/logout');
      setUser(null);
      navigate('/signin');
    } catch (error) {
      console.error(error);
    }
  };

  const menuItems = [
    {
      key: '/invoices',
      label: (
        <Link to='/invoices'>{t('layout.allInvoices', 'All Invoices')}</Link>
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
  ];

  return (
    <AntLayout className='min-h-screen'>
      <Header className='bg-gray-900 text-white flex items-center justify-between px-6 md:px-12'>
        {/* Mobile Menu Button */}
        <button
          className='block md:hidden text-white text-xl'
          onClick={() => setMobileMenuOpen(true)}
        >
          <MenuOutlined />
        </button>

        {/* Logo */}
        <div className='text-white font-bold text-xl'>
          <Link to='/'>{t('layout.title', 'Modern Invoicing')}</Link>
        </div>

        {/* Desktop Menu */}
        <Menu
          theme='dark'
          mode='horizontal'
          selectedKeys={selectedKeys}
          items={menuItems}
          className='hidden md:flex'
        />

        {/* User Actions */}
        <div className='flex items-center space-x-4'>
          {user ? (
            <Button type='text' onClick={handleSignOut}>
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

      {/* Mobile Drawer */}
      <Drawer
        title={t('layout.menu', 'Menu')}
        placement='left'
        onClose={() => setMobileMenuOpen(false)}
        open={mobileMenuOpen}
      >
        <Menu
          mode='vertical'
          selectedKeys={selectedKeys}
          items={menuItems}
          onClick={() => setMobileMenuOpen(false)}
        />
      </Drawer>

      <Content className='p-6'>{children}</Content>
    </AntLayout>
  );
};

export default Layout;
