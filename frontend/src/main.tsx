// src/index.tsx
import React from 'react';
import { createRoot } from 'react-dom/client';
import { BrowserRouter } from 'react-router-dom'; // ✅ Import Router
import App from './App';
import { AuthProvider } from './contexts/AuthContext';
import { InvoiceProvider } from './contexts/InvoiceContext';

const root = createRoot(document.getElementById('root') as HTMLElement);

root.render(
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
  </React.StrictMode>
);
