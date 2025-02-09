import React from 'react';
import { Button, Label, TextInput } from 'flowbite-react';
import { Link, useNavigate } from 'react-router-dom';
import axios from 'axios';
import { useTranslation } from 'react-i18next';
import { useAuth } from '../contexts/AuthContext';

const SignInPage: React.FC = () => {
  const navigate = useNavigate();
  const { setUser } = useAuth();
  const { t } = useTranslation();

  const handleSignIn = async (event: React.FormEvent) => {
    event.preventDefault();
    const form = event.target as HTMLFormElement;
    const email = (form.elements.namedItem('email') as HTMLInputElement).value;
    const password = (form.elements.namedItem('password') as HTMLInputElement)
      .value;

    try {
      const response = await axios.post('/api/auth/signin', {
        email,
        password,
      });
      setUser(response.data.user);
      navigate('/invoices');
    } catch (error) {
      console.error(error);
    }
  };

  return (
    <div className='max-w-md mx-auto p-6'>
      <h1 className='text-2xl font-bold mb-4'>{t('form.signIn', 'Sign In')}</h1>
      <form onSubmit={handleSignIn} className='space-y-4'>
        <div>
          <Label htmlFor='email'>{t('form.email', 'Email')}</Label>
          <TextInput id='email' name='email' type='email' required />
        </div>
        <div>
          <Label htmlFor='password'>{t('form.password', 'Password')}</Label>
          <TextInput id='password' name='password' type='password' required />
        </div>
        <Button type='submit' color='blue'>
          {t('form.signIn', 'Sign In')}
        </Button>
      </form>
      <p className='mt-4'>
        {t('form.noAccount', "Don't have an account?")}{' '}
        <Link to='/signup' className='text-blue-600'>
          {t('form.signUp', 'Sign Up')}
        </Link>
      </p>
    </div>
  );
};

export default SignInPage;
