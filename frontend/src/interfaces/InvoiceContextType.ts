import { Invoice, InvoicePayload } from '.';

export interface InvoiceContextType {
  getInvoices: () => Promise<Invoice[]>;
  getInvoice: (id: number) => Promise<Invoice>;
  createInvoice: (
    payload: InvoicePayload
  ) => Promise<Invoice | { message: string }>;
  updateInvoice: (
    id: number,
    payload: InvoicePayload
  ) => Promise<Invoice | { message: string }>;
  deleteInvoice: (id: number) => Promise<{ message: string }>;
  generateInvoicePDF: (
    id: number
  ) => Promise<{ message: string; pdf_path?: string }>;
}
