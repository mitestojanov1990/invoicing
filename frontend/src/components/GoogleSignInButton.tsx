// src/components/GoogleSignInButton.tsx
import React from 'react';
import { Button } from 'antd';
import { useTranslation } from 'react-i18next';

const GoogleSignInButton: React.FC = () => {
  const { t } = useTranslation();

  const handleGoogleSignIn = () => {
    // Redirect to PHP endpoint that starts the Google OAuth flow
    window.location.href = '/auth/google';
  };

  return (
    <Button onClick={handleGoogleSignIn} type='default'>
      {t('form.signInWithGoogle', 'Sign In with Google')}
    </Button>
  );
};

export default GoogleSignInButton;
