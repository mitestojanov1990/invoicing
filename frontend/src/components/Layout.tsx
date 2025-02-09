import React, { useState, useEffect } from 'react';
import {
  Layout as AntLayout,
  Menu,
  Button,
  Drawer,
  Modal,
  Form,
  Input,
  message,
} from 'antd';
import { MenuOutlined } from '@ant-design/icons';
import { Link, useLocation, useNavigate } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import LanguageSwitcher from './LanguageSwitcher';
import { useAuth } from '../contexts/AuthContext';
import axios from 'axios';

const { Header, Sider, Content } = AntLayout;

const Layout: React.FC<{ children: React.ReactNode }> = ({ children }) => {
  const { t } = useTranslation();
  const location = useLocation();
  const navigate = useNavigate();
  const { user, setUser, signIn } = useAuth();
  const selectedKeys = [location.pathname];
  const [menuCollapsed, setMenuCollapsed] = useState(true);
  const [authModalVisible, setAuthModalVisible] = useState(false);
  const [isSignUp, setIsSignUp] = useState(false);
  const [form] = Form.useForm();

  useEffect(() => {
    const updateIsMobile = () => {
      setMenuCollapsed(window.innerWidth < 768);
    };
    window.addEventListener('resize', updateIsMobile);
    return () => window.removeEventListener('resize', updateIsMobile);
  }, []);

  const handleSignOut = async () => {
    try {
      await axios.get('/logout');
      setUser(null);
      navigate('/signin');
    } catch (error) {
      console.error(error);
    }
  };

  const handleAuthSubmit = async (values: any) => {
    if (isSignUp) {
      try {
        const response = await axios.post('/api/auth/signup', values);
        setUser(response.data.user);
        message.success(t('messages.signUpSuccess', 'Signed up successfully'));
        setAuthModalVisible(false);
      } catch (error) {
        message.error(t('messages.signUpFailed', 'Sign up failed'));
      }
    } else {
      try {
        await signIn(values.email, values.password);
        message.success(t('messages.signInSuccess', 'Signed in successfully'));
        setAuthModalVisible(false);
      } catch (error) {
        message.error(t('messages.signInFailed', 'Sign in failed'));
      }
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
      <Sider
        collapsible
        collapsed={menuCollapsed}
        onCollapse={setMenuCollapsed}
        className='h-screen'
      >
        <div className='p-4 text-white text-xl font-bold text-center'>
          {t('layout.title', 'Modern Invoicing')}
        </div>
        <Menu
          theme='dark'
          mode='vertical'
          selectedKeys={selectedKeys}
          items={menuItems}
        />
        <div className='p-4 text-center'>
          {user ? (
            <Button type='text' onClick={handleSignOut}>
              {t('layout.signOut', 'Sign Out')}
            </Button>
          ) : (
            <Button type='primary' onClick={() => setAuthModalVisible(true)}>
              {t('layout.signIn', 'Sign In')}
            </Button>
          )}
          <div className='mt-4'>
            <LanguageSwitcher />
          </div>
        </div>
      </Sider>
      <AntLayout>
        <Header className='bg-gray-900 text-white flex items-center justify-between px-4 md:px-12 h-16 w-full'>
          <button
            className='text-white text-xl'
            onClick={() => setMenuCollapsed(!menuCollapsed)}
          >
            <MenuOutlined />
          </button>
        </Header>
        <Content className='p-6 mt-6'>{children}</Content>
      </AntLayout>

      {/* Authentication Modal */}
      <Modal
        title={
          isSignUp ? t('form.signUp', 'Sign Up') : t('form.signIn', 'Sign In')
        }
        open={authModalVisible}
        onCancel={() => setAuthModalVisible(false)}
        footer={null}
      >
        <Form form={form} layout='vertical' onFinish={handleAuthSubmit}>
          {isSignUp && (
            <Form.Item
              name='name'
              label={t('form.name', 'Name')}
              rules={[{ required: true }]}
            >
              {' '}
              <Input />{' '}
            </Form.Item>
          )}
          <Form.Item
            name='email'
            label={t('form.email', 'Email')}
            rules={[{ required: true, type: 'email' }]}
          >
            {' '}
            <Input />{' '}
          </Form.Item>
          <Form.Item
            name='password'
            label={t('form.password', 'Password')}
            rules={[{ required: true }]}
          >
            {' '}
            <Input.Password />{' '}
          </Form.Item>
          {isSignUp && (
            <Form.Item
              name='confirmPassword'
              label={t('form.confirmPassword', 'Confirm Password')}
              rules={[{ required: true }]}
            >
              {' '}
              <Input.Password />{' '}
            </Form.Item>
          )}
          <Form.Item>
            <Button type='primary' htmlType='submit'>
              {isSignUp
                ? t('form.signUp', 'Sign Up')
                : t('form.signIn', 'Sign In')}
            </Button>
          </Form.Item>
        </Form>
        <Button type='link' onClick={() => setIsSignUp(!isSignUp)}>
          {isSignUp
            ? t('form.haveAccount', 'Already have an account?')
            : t('form.noAccount', "Don't have an account?")}
        </Button>
      </Modal>
    </AntLayout>
  );
};

export default Layout;
