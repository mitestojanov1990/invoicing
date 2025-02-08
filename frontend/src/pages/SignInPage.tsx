// src/pages/SignInPage.tsx
import React from 'react';
import { Form, Input, Button, message } from 'antd';
import { Link, useNavigate } from 'react-router-dom';
import axios, { AxiosError } from 'axios';
import { useTranslation } from 'react-i18next';
import { useAuth } from '../contexts/AuthContext';

interface SignInValues {
  email: string;
  password: string;
}

const SignInPage: React.FC = () => {
  const [form] = Form.useForm<SignInValues>();
  const navigate = useNavigate();
  const { setUser } = useAuth();
  const { t } = useTranslation();

  const onFinish = async (values: SignInValues) => {
    try {
      const response = await axios.post('/api/auth/signin', values);
      setUser(response.data.user);
      message.success(t('messages.signInSuccess', 'Signed in successfully'));
      navigate('/invoices');
    } catch (error: unknown) {
      if (axios.isAxiosError(error)) {
        message.error(
          `${t('messages.signInFailed', 'Sign in failed')}: ${error.message}`
        );
      } else {
        message.error(t('messages.signInFailed', 'Sign in failed'));
      }
    }
  };

  return (
    <div className='max-w-md mx-auto p-6'>
      <h1 className='text-2xl font-bold mb-4'>{t('form.signIn', 'Sign In')}</h1>
      <Form form={form} layout='vertical' onFinish={onFinish}>
        <Form.Item
          name='email'
          label={t('form.email', 'Email')}
          rules={[
            {
              required: true,
              message: t('form.emailRequired', 'Email is required'),
            },
            { type: 'email', message: t('form.emailInvalid', 'Invalid email') },
          ]}
        >
          <Input />
        </Form.Item>
        <Form.Item
          name='password'
          label={t('form.password', 'Password')}
          rules={[
            {
              required: true,
              message: t('form.passwordRequired', 'Password is required'),
            },
          ]}
        >
          <Input.Password />
        </Form.Item>
        <Form.Item>
          <Button type='primary' htmlType='submit'>
            {t('form.signIn', 'Sign In')}
          </Button>
        </Form.Item>
      </Form>
      <Button
        type='default'
        onClick={() => (window.location.href = '/auth/google')}
        className='mb-4'
      >
        {t('form.signInWithGoogle', 'Sign In with Google')}
      </Button>
      <div>
        {t('form.noAccount', "Don't have an account?")}{' '}
        <Link to='/signup'>{t('form.signUp', 'Sign Up')}</Link>
      </div>
    </div>
  );
};

export default SignInPage;
