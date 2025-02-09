// src/index.tsx
import React from 'react';
import ReactDOM from 'react-dom';
import { BrowserRouter } from 'react-router-dom'; // ✅ Import Router
import App from './App';
import { AuthProvider } from './contexts/AuthContext';
import { InvoiceProvider } from './contexts/InvoiceContext';

ReactDOM.render(
  <React.StrictMode>
    <BrowserRouter>
      {' '}
      {/* ✅ Wrap the whole app */}
      <AuthProvider>
        <InvoiceProvider>
          <App />
        </InvoiceProvider>
      </AuthProvider>
    </BrowserRouter>
  </React.StrictMode>,
  document.getElementById('root')
);
