import { Moment } from 'moment';

export interface InvoiceFormValues {
  invoice_number: string;
  invoice_date: Moment;
  to_name: string;
  city: string;
  invoice_type: number;
}
