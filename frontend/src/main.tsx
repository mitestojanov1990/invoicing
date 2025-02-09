// src/App.tsx or src/index.tsx
import React from 'react';
import ReactDOM from 'react-dom';
import App from './App';
import { AuthProvider } from './contexts/AuthContext';
import { InvoiceProvider } from './contexts/InvoiceContext';

ReactDOM.render(
  <React.StrictMode>
    <AuthProvider>
      <InvoiceProvider>
        {' '}
        {/* ✅ Wrap the whole app */}
        <App />
      </InvoiceProvider>
    </AuthProvider>
  </React.StrictMode>,
  document.getElementById('root')
);
