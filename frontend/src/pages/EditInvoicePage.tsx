// src/pages/EditInvoicePage.tsx
import React, { useEffect, useState } from 'react';
import {
  Form,
  Input,
  DatePicker,
  Select,
  Button,
  Table,
  InputNumber,
  message,
} from 'antd';
import type { ColumnsType } from 'antd/es/table';
import { useNavigate, useParams } from 'react-router-dom';
import axios, { AxiosError } from 'axios';
import moment from 'moment';
import {
  Invoice,
  InvoiceLine,
  InvoiceFormValues,
  InvoicePayload,
} from '../interfaces';
import { useTranslation } from 'react-i18next';

const EditInvoicePage: React.FC = () => {
  const [form] = Form.useForm<InvoiceFormValues>();
  const [lines, setLines] = useState<InvoiceLine[]>([]);
  const { id } = useParams<{ id: string }>();
  const navigate = useNavigate();
  const { t } = useTranslation();

  const fetchInvoice = async (): Promise<void> => {
    try {
      const response = await axios.get<Invoice>(`/api/invoices/${id}`);
      const invoice = response.data;
      form.setFieldsValue({
        invoice_number: invoice.invoice_number,
        invoice_date: moment(invoice.invoice_date),
        to_name: invoice.to_name,
        city: invoice.city,
        invoice_type: invoice.invoice_type,
      });
      setLines(invoice.lines);
    } catch (error: unknown) {
      if (axios.isAxiosError(error)) {
        const axiosError: AxiosError = error;
        message.error(
          `${t('messages.invoiceFetchedFailed')}: ${axiosError.message}`
        );
      } else {
        message.error(t('messages.invoiceFetchedFailed'));
      }
    }
  };

  useEffect(() => {
    if (id) {
      fetchInvoice();
    }
  }, [id]);

  const addLine = (): void => {
    setLines([...lines, { description: '', quantity: 0, price: 0, total: 0 }]);
  };

  const updateLine = <K extends keyof InvoiceLine>(
    index: number,
    key: K,
    value: InvoiceLine[K]
  ): void => {
    const newLines: InvoiceLine[] = [...lines];
    newLines[index] = { ...newLines[index], [key]: value };

    if (key === 'quantity' || key === 'price') {
      newLines[index].total =
        Number(newLines[index].quantity) * Number(newLines[index].price);
    }
    setLines(newLines);
  };

  const removeLine = (index: number): void => {
    setLines(lines.filter((_, i) => i !== index));
  };

  const columns: ColumnsType<InvoiceLine> = [
    {
      title: t('form.invoiceLinesDescription', 'Description'),
      dataIndex: 'description',
      key: 'description',
      render: (_: string, record: InvoiceLine, index: number) => (
        <Input
          value={record.description}
          onChange={(e) => updateLine(index, 'description', e.target.value)}
        />
      ),
    },
    {
      title: t('form.invoiceLinesQuantity', 'Quantity'),
      dataIndex: 'quantity',
      key: 'quantity',
      render: (_: number, record: InvoiceLine, index: number) => (
        <InputNumber
          value={record.quantity}
          onChange={(value) => {
            if (value !== null && value !== undefined) {
              updateLine(index, 'quantity', value);
            }
          }}
        />
      ),
    },
    {
      title: t('form.invoiceLinesPrice', 'Price'),
      dataIndex: 'price',
      key: 'price',
      render: (_: number, record: InvoiceLine, index: number) => (
        <InputNumber
          value={record.price}
          onChange={(value) => {
            if (value !== null && value !== undefined) {
              updateLine(index, 'price', value);
            }
          }}
        />
      ),
    },
    {
      title: t('form.invoiceLinesTotal', 'Total'),
      dataIndex: 'total',
      key: 'total',
      render: (total: number) => total.toFixed(2),
    },
    {
      title: t('form.actions', 'Actions'),
      key: 'actions',
      render: (_: unknown, __: InvoiceLine, index: number) => (
        <Button danger onClick={() => removeLine(index)}>
          {t('form.remove', 'Remove')}
        </Button>
      ),
    },
  ];

  const onFinish = async (values: InvoiceFormValues): Promise<void> => {
    const payload: InvoicePayload = {
      ...values,
      invoice_date: values.invoice_date.format('YYYY-MM-DD'),
      lines,
    };

    try {
      await axios.put(`/api/invoices/${id}`, payload);
      message.success(t('messages.invoiceUpdatedSuccess'));
      navigate('/invoices');
    } catch (error: unknown) {
      if (axios.isAxiosError(error)) {
        const axiosError: AxiosError = error;
        message.error(
          `${t('messages.invoiceUpdateFailed')}: ${axiosError.message}`
        );
      } else {
        message.error(t('messages.invoiceUpdateFailed'));
      }
    }
  };

  return (
    <div>
      <h1 className='text-2xl font-bold mb-4'>
        {t('form.editInvoice', 'Edit Invoice')}
      </h1>
      <Form form={form} layout='vertical' onFinish={onFinish}>
        <Form.Item
          name='invoice_number'
          label={t('form.invoiceNumber', 'Invoice Number')}
          rules={[
            {
              required: true,
              message: t(
                'form.invoiceNumberRequired',
                'Please input the invoice number'
              ),
            },
          ]}
        >
          <Input />
        </Form.Item>
        <Form.Item
          name='invoice_date'
          label={t('form.invoiceDate', 'Invoice Date')}
          rules={[
            {
              required: true,
              message: t(
                'form.invoiceDateRequired',
                'Please select the invoice date'
              ),
            },
          ]}
        >
          <DatePicker />
        </Form.Item>
        <Form.Item name='to_name' label={t('form.toName', 'To Name')}>
          <Input />
        </Form.Item>
        <Form.Item name='city' label={t('form.city', 'City')}>
          <Input />
        </Form.Item>
        <Form.Item
          name='invoice_type'
          label={t('form.invoiceType', 'Type')}
          initialValue={1}
        >
          <Select>
            <Select.Option value={1}>
              {t('invoiceType.1', 'Invoice')}
            </Select.Option>
            <Select.Option value={2}>
              {t('invoiceType.2', 'Proforma Invoice')}
            </Select.Option>
            <Select.Option value={3}>
              {t('invoiceType.3', 'Offer')}
            </Select.Option>
          </Select>
        </Form.Item>
        <Form.Item label={t('form.invoiceLines', 'Invoice Lines')}>
          <Table
            dataSource={lines}
            columns={columns}
            pagination={false}
            rowKey={(_, index) => (index ? index.toString() : 'ERROR')}
            footer={() => (
              <Button type='dashed' onClick={addLine} block>
                {t('form.addLine', 'Add Line')}
              </Button>
            )}
          />
        </Form.Item>
        <Form.Item>
          <Button type='primary' htmlType='submit'>
            {t('form.updateInvoice', 'Update Invoice')}
          </Button>
        </Form.Item>
      </Form>
    </div>
  );
};

export default EditInvoicePage;
