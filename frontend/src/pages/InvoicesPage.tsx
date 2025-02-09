// src/pages/InvoicesPage.tsx
import React, { useEffect, useState } from 'react';
import { useTranslation } from 'react-i18next';
import { useInvoice } from '../contexts/InvoiceContext';
import { Invoice } from '../interfaces';
import { Table, Button, Spinner } from 'flowbite-react';
import { Link } from 'react-router-dom';

const InvoicesPage: React.FC = () => {
  const [invoices, setInvoices] = useState<Invoice[]>([]);
  const [loading, setLoading] = useState<boolean>(true);
  const { getInvoices, deleteInvoice } = useInvoice();
  const { t } = useTranslation();

  // Fetch invoices on component mount
  useEffect(() => {
    const fetchInvoices = async () => {
      try {
        const data = await getInvoices();
        setInvoices(data);
      } catch (error) {
        console.error('Failed to fetch invoices:', error);
      } finally {
        setLoading(false);
      }
    };

    fetchInvoices();
  }, [getInvoices]);

  const handleDelete = async (id: number): Promise<void> => {
    try {
      await deleteInvoice(id);
      setInvoices(invoices.filter((invoice) => invoice.id !== id));
    } catch (error) {
      console.error('Failed to delete invoice:', error);
    }
  };

  return (
    <div className='max-w-6xl mx-auto p-6'>
      <h1 className='text-2xl font-bold mb-4'>
        {t('form.allInvoices', 'All Invoices')}
      </h1>

      {loading ? (
        <div className='flex justify-center mt-8'>
          <Spinner size='xl' />
        </div>
      ) : (
        <Table>
          <Table.Head>
            <Table.HeadCell>{t('table.id', 'ID')}</Table.HeadCell>
            <Table.HeadCell>
              {t('table.invoiceNumber', 'Number')}
            </Table.HeadCell>
            <Table.HeadCell>{t('table.invoiceDate', 'Date')}</Table.HeadCell>
            <Table.HeadCell>{t('table.toName', 'To')}</Table.HeadCell>
            <Table.HeadCell>{t('table.city', 'City')}</Table.HeadCell>
            <Table.HeadCell>{t('table.invoiceType', 'Type')}</Table.HeadCell>
            <Table.HeadCell>{t('table.actions', 'Actions')}</Table.HeadCell>
          </Table.Head>
          <Table.Body>
            {invoices.map((invoice) => (
              <Table.Row key={invoice.id}>
                <Table.Cell>{invoice.id}</Table.Cell>
                <Table.Cell>{invoice.invoice_number}</Table.Cell>
                <Table.Cell>{invoice.invoice_date}</Table.Cell>
                <Table.Cell>{invoice.to_name}</Table.Cell>
                <Table.Cell>{invoice.city}</Table.Cell>
                <Table.Cell>
                  {t(`invoiceType.${invoice.invoice_type}`, 'Unknown')}
                </Table.Cell>
                <Table.Cell>
                  <div className='flex space-x-2'>
                    <Link to={`/invoices/${invoice.id}/edit`}>
                      <Button color='blue'>{t('table.edit', 'Edit')}</Button>
                    </Link>
                    <Button
                      color='red'
                      onClick={() => handleDelete(invoice.id)}
                    >
                      {t('table.delete', 'Delete')}
                    </Button>
                    <Button
                      color='gray'
                      onClick={() =>
                        window.open(`/api/invoices/${invoice.id}/pdf`, '_blank')
                      }
                    >
                      {t('table.pdf', 'PDF')}
                    </Button>
                  </div>
                </Table.Cell>
              </Table.Row>
            ))}
          </Table.Body>
        </Table>
      )}
    </div>
  );
};

export default InvoicesPage;
