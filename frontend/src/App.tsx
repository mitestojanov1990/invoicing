// src/App.tsx
import { Routes, Route, Navigate } from 'react-router-dom';
import Layout from './components/Layout';
import InvoicesPage from './pages/InvoicesPage';
import CreateInvoicePage from './pages/CreateInvoicePage';
import EditInvoicePage from './pages/EditInvoicePage';
import SignInPage from './pages/SignInPage';
import SignUpPage from './pages/SignUpPage';
import ProtectedRoute from './components/ProtectedRoute';

function App() {
  return (
    <Layout>
      <Routes>
        <Route path='/' element={<Navigate to='/invoices' />} />
        <Route path='/signin' element={<SignInPage />} />
        <Route path='/signup' element={<SignUpPage />} />
        <Route
          path='/invoices'
          element={
            <ProtectedRoute>
              <InvoicesPage />
            </ProtectedRoute>
          }
        />
        <Route
          path='/invoices/create'
          element={
            <ProtectedRoute>
              <CreateInvoicePage />
            </ProtectedRoute>
          }
        />
        <Route
          path='/invoices/:id/edit'
          element={
            <ProtectedRoute>
              <EditInvoicePage />
            </ProtectedRoute>
          }
        />
        <Route path='*' element={<div>404 Not Found</div>} />
      </Routes>
    </Layout>
  );
}

export default App;
