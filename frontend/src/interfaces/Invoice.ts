import { InvoiceLine } from '.';

export interface Invoice {
  id: number;
  invoice_number: string;
  invoice_date: string;
  to_name: string;
  city: string;
  invoice_type: number;
  lines: InvoiceLine[];
}
