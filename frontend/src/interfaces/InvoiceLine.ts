export interface InvoiceLine {
  description: string;
  quantity: number;
  price: number;
  total: number;
  id: number;
}

export interface EditableInvoiceLine extends InvoiceLine {
  tempId?: string;
}
