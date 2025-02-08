// src/contexts/InvoiceContext.tsx
import React, { createContext, useContext } from 'react';
import axios from 'axios';
import { Invoice, InvoicePayload, InvoiceContextType } from '../interfaces';

const InvoiceContext = createContext<InvoiceContextType | undefined>(undefined);

export const InvoiceProvider: React.FC<{ children: React.ReactNode }> = ({
  children,
}) => {
  const getInvoices = async (): Promise<Invoice[]> => {
    const response = await axios.get<Invoice[]>('/api/invoices');
    return response.data;
  };

  const getInvoice = async (id: number): Promise<Invoice> => {
    const response = await axios.get<Invoice>(`/api/invoices/${id}`);
    return response.data;
  };

  const createInvoice = async (
    payload: InvoicePayload
  ): Promise<Invoice | { message: string }> => {
    const response = await axios.post('/api/invoices', payload);
    return response.data;
  };

  const updateInvoice = async (
    id: number,
    payload: InvoicePayload
  ): Promise<Invoice | { message: string }> => {
    const response = await axios.put(`/api/invoices/${id}`, payload);
    return response.data;
  };

  const deleteInvoice = async (id: number): Promise<{ message: string }> => {
    const response = await axios.delete(`/api/invoices/${id}`);
    return response.data;
  };

  const generateInvoicePDF = async (
    id: number
  ): Promise<{ message: string; pdf_path?: string }> => {
    const response = await axios.get(`/api/invoices/${id}/pdf`);
    return response.data;
  };

  return (
    <InvoiceContext.Provider
      value={{
        getInvoices,
        getInvoice,
        createInvoice,
        updateInvoice,
        deleteInvoice,
        generateInvoicePDF,
      }}
    >
      {children}
    </InvoiceContext.Provider>
  );
};

export const useInvoice = (): InvoiceContextType => {
  const context = useContext(InvoiceContext);
  if (!context) {
    throw new Error('useInvoice must be used within an InvoiceProvider');
  }
  return context;
};

export default InvoiceContext;
