import React from 'react';
import {
  Navbar,
  NavbarBrand,
  NavbarCollapse,
  NavbarLink,
} from 'flowbite-react';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { useAuth } from '../contexts/AuthContext';
import axios from 'axios';

const Layout: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const { t } = useTranslation();
  const location = useLocation();
  const navigate = useNavigate();
  const { user, setUser } = useAuth();

  const handleSignOut = async () => {
    try {
      await axios.post('/api/auth/signout', {}, { withCredentials: true });
      setUser(null);
      navigate('/signin');
    } catch (error) {
      console.error(error);
    }
  };

  return (
    <div className='min-h-screen'>
      <Navbar>
        {/* Use the Link component as a wrapper for NavbarBrand to avoid <a> nesting */}
        <Link to='/invoices'>
          <NavbarBrand>
            <span className='text-xl font-bold'>
              {t('layout.title', 'Modern Invoicing')}
            </span>
          </NavbarBrand>
        </Link>
        <NavbarCollapse>
          <NavbarLink
            as={Link}
            to='/invoices'
            active={location.pathname === '/invoices'}
          >
            {t('layout.allInvoices', 'All Invoices')}
          </NavbarLink>
          <NavbarLink
            as={Link}
            to='/invoices/create'
            active={location.pathname === '/invoices/create'}
          >
            {t('layout.createInvoice', 'Create Invoice')}
          </NavbarLink>
        </NavbarCollapse>
        <div className='flex items-center space-x-4'>
          {user ? (
            <button className='text-red-500' onClick={handleSignOut}>
              {t('layout.signOut', 'Sign Out')}
            </button>
          ) : (
            <Link to='/signin'>
              <button className='bg-blue-500 text-white px-4 py-2 rounded-lg'>
                {t('layout.signIn', 'Sign In')}
              </button>
            </Link>
          )}
        </div>
      </Navbar>
      <main className='p-6'>{children}</main>
    </div>
  );
};

export default Layout;
