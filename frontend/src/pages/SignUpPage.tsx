import React from 'react';
import { Button, Label, TextInput } from 'flowbite-react';
import { useNavigate } from 'react-router-dom';
import axios from 'axios';
import { useTranslation } from 'react-i18next';
import { useAuth } from '../contexts/AuthContext';

const SignUpPage: React.FC = () => {
  const navigate = useNavigate();
  const { setUser } = useAuth();
  const { t } = useTranslation();

  const handleSignUp = async (event: React.FormEvent) => {
    event.preventDefault();
    const form = event.target as HTMLFormElement;
    const name = (form.elements.namedItem('name') as HTMLInputElement).value;
    const email = (form.elements.namedItem('email') as HTMLInputElement).value;
    const password = (form.elements.namedItem('password') as HTMLInputElement)
      .value;

    try {
      const response = await axios.post('/api/auth/signup', {
        name,
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
      <h1 className='text-2xl font-bold mb-4'>{t('form.signUp', 'Sign Up')}</h1>
      <form onSubmit={handleSignUp} className='space-y-4'>
        <div>
          <Label htmlFor='name'>{t('form.name', 'Name')}</Label>
          <TextInput id='name' name='name' type='text' required />
        </div>
        <Button type='submit' color='blue'>
          {t('form.signUp', 'Sign Up')}
        </Button>
      </form>
    </div>
  );
};

export default SignUpPage;
