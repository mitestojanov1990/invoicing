// src/pages/SignUpPage.tsx
import React from 'react';
import { Form, Input, Button, message } from 'antd';
import { Link, useNavigate } from 'react-router-dom';
import axios, { AxiosError } from 'axios';
import { useTranslation } from 'react-i18next';
import { useAuth } from '../contexts/AuthContext';

interface SignUpValues {
  name: string;
  email: string;
  password: string;
  confirmPassword: string;
}

const SignUpPage: React.FC = () => {
  const [form] = Form.useForm<SignUpValues>();
  const navigate = useNavigate();
  const { setUser } = useAuth();
  const { t } = useTranslation();

  const onFinish = async (values: SignUpValues) => {
    if (values.password !== values.confirmPassword) {
      message.error(t('messages.passwordMismatch', 'Passwords do not match'));
      return;
    }
    try {
      const response = await axios.post('/api/auth/signup', {
        name: values.name,
        email: values.email,
        password: values.password,
      });
      setUser(response.data.user);
      message.success(t('messages.signUpSuccess', 'Signed up successfully'));
      navigate('/invoices');
    } catch (error: unknown) {
      if (axios.isAxiosError(error)) {
        message.error(
          `${t('messages.signUpFailed', 'Sign up failed')}: ${error.message}`
        );
      } else {
        message.error(t('messages.signUpFailed', 'Sign up failed'));
      }
    }
  };

  return (
    <div className='max-w-md mx-auto p-6'>
      <h1 className='text-2xl font-bold mb-4'>{t('form.signUp', 'Sign Up')}</h1>
      <Form form={form} layout='vertical' onFinish={onFinish}>
        <Form.Item
          name='name'
          label={t('form.name', 'Name')}
          rules={[
            {
              required: true,
              message: t('form.nameRequired', 'Name is required'),
            },
          ]}
        >
          <Input />
        </Form.Item>
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
        <Form.Item
          name='confirmPassword'
          label={t('form.confirmPassword', 'Confirm Password')}
          rules={[
            {
              required: true,
              message: t(
                'form.confirmPasswordRequired',
                'Please confirm your password'
              ),
            },
          ]}
        >
          <Input.Password />
        </Form.Item>
        <Form.Item>
          <Button type='primary' htmlType='submit'>
            {t('form.signUp', 'Sign Up')}
          </Button>
        </Form.Item>
      </Form>
      <div>
        {t('form.haveAccount', 'Already have an account?')}{' '}
        <Link to='/signin'>{t('form.signIn', 'Sign In')}</Link>
      </div>
    </div>
  );
};

export default SignUpPage;
