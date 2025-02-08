import React from 'react';
import { Button } from 'antd';
import i18n from '../i18n';

const LanguageSwitcher: React.FC = () => {
  return (
    <div>
      <Button onClick={() => i18n.changeLanguage('mk')}>MK</Button>
      <Button onClick={() => i18n.changeLanguage('en')}>EN</Button>
    </div>
  );
};

export default LanguageSwitcher;
