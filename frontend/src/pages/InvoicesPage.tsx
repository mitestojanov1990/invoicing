// src/pages/InvoicesPage.tsx
import React, { useEffect, useState } from 'react';
import { Table, Button, message } from 'antd';
import type { ColumnsType } from 'antd/es/table';
import { Link } from 'react-router-dom';
import { useTranslation } from 'react-i18next';
import { useInvoice } from '../contexts/InvoiceContext';
import { Invoice } from '../interfaces'; // Ensure this is consistent with your context types

const InvoicesPage: React.FC = () => {
  const [invoices, setInvoices] = useState<Invoice[]>([]);
  const [loading, setLoading] = useState<boolean>(false);
  const { t } = useTranslation();
  const { getInvoices, deleteInvoice } = useInvoice();

  const fetchInvoices = async (): Promise<void> => {
    setLoading(true);
    try {
      const data = await getInvoices();
      setInvoices(data);
    } catch (error) {
      console.log(error);
      message.error(
        t('messages.fetchInvoicesFailed', 'Failed to fetch invoices')
      );
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchInvoices();
  }, []);

  const handleDelete = async (id: number): Promise<void> => {
    try {
      await deleteInvoice(id);
      message.success(
        t('messages.invoiceDeletedSuccess', 'Invoice deleted successfully.')
      );
      fetchInvoices();
    } catch (error) {
      console.log(error);
      message.error(
        t('messages.invoiceDeleteFailed', 'Failed to delete invoice')
      );
    }
  };

  const columns: ColumnsType<Invoice> = [
    { title: t('table.id', 'ID'), dataIndex: 'id', key: 'id' },
    {
      title: t('table.invoiceNumber', 'Number'),
      dataIndex: 'invoice_number',
      key: 'invoice_number',
    },
    {
      title: t('table.invoiceDate', 'Date'),
      dataIndex: 'invoice_date',
      key: 'invoice_date',
    },
    { title: t('table.toName', 'To'), dataIndex: 'to_name', key: 'to_name' },
    { title: t('table.city', 'City'), dataIndex: 'city', key: 'city' },
    {
      title: t('table.invoiceType', 'Type'),
      dataIndex: 'invoice_type',
      key: 'invoice_type',
      render: (type: number) => t(`invoiceType.${type}`, 'Unknown'),
    },
    {
      title: t('table.actions', 'Actions'),
      key: 'actions',
      render: (_: unknown, record: Invoice) => (
        <div className='space-x-2'>
          <Link to={`/invoices/${record.id}/edit`}>
            <Button type='primary'>{t('table.edit', 'Edit')}</Button>
          </Link>
          <Button danger onClick={() => handleDelete(record.id)}>
            {t('table.delete', 'Delete')}
          </Button>
          <Button
            onClick={() =>
              window.open(`/api/invoices/${record.id}/pdf`, '_blank')
            }
          >
            {t('table.pdf', 'PDF')}
          </Button>
        </div>
      ),
    },
  ];

  return (
    <div>
      <h1 className='text-2xl font-bold mb-4'>
        {t('form.allInvoices', 'All Invoices')}
      </h1>
      <Table
        dataSource={invoices}
        columns={columns}
        rowKey='id'
        loading={loading}
      />
    </div>
  );
};

export default InvoicesPage;
