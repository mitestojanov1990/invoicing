// src/pages/CreateInvoicePage.tsx
import React, { useState } from 'react';
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
import { useNavigate } from 'react-router-dom';
import { v4 as uuidv4 } from 'uuid';
import moment from 'moment';
import { useTranslation } from 'react-i18next';
import { useInvoice } from '../contexts/InvoiceContext';
import {
  InvoiceFormValues,
  InvoicePayload,
  EditableInvoiceLine,
} from '../interfaces';

const CreateInvoicePage: React.FC = () => {
  const [form] = Form.useForm<InvoiceFormValues>();
  const [lines, setLines] = useState<EditableInvoiceLine[]>([]);
  const navigate = useNavigate();
  const { t } = useTranslation();
  const { createInvoice } = useInvoice();

  const addLine = (): void => {
    setLines([
      ...lines,
      {
        tempId: uuidv4(),
        description: '',
        quantity: 0,
        price: 0,
        total: 0,
        id: 0,
      },
    ]);
  };

  const updateLine = <K extends keyof EditableInvoiceLine>(
    index: number,
    key: K,
    value: EditableInvoiceLine[K]
  ): void => {
    const newLines = [...lines];
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

  const onFinish = async (values: InvoiceFormValues): Promise<void> => {
    const payload: InvoicePayload = {
      ...values,
      invoice_date: values.invoice_date.format('YYYY-MM-DD'),
      lines,
    };

    try {
      await createInvoice(payload);
      message.success(
        t('messages.invoiceCreatedSuccess', 'Invoice created successfully.')
      );
      navigate('/invoices');
    } catch (error) {
      console.log(error);
      message.error(
        t('messages.invoiceCreateFailed', 'Failed to create invoice')
      );
    }
  };

  return (
    <div>
      <h1 className='text-2xl font-bold mb-4'>
        {t('form.createInvoice', 'Create Invoice')}
      </h1>
      <Form form={form} layout='vertical' onFinish={onFinish}>
        <Form.Item
          name='invoice_number'
          label={t('form.invoiceNumber', 'Invoice Number')}
          rules={[{ required: true }]}
        >
          <Input />
        </Form.Item>
        <Form.Item
          name='invoice_date'
          label={t('form.invoiceDate', 'Invoice Date')}
          initialValue={moment()}
          rules={[{ required: true }]}
        >
          <DatePicker />
        </Form.Item>
        <Form.Item name='to_name' label={t('form.toName', 'To Name')}>
          <Input />
        </Form.Item>
        <Form.Item name='city' label={t('form.city', 'City')}>
          <Input />
        </Form.Item>
        <Form.Item name='invoice_type' label={t('form.invoiceType', 'Type')}>
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
        <Table
          dataSource={lines}
          columns={[
            {
              title: t('form.invoiceLinesDescription', 'Description'),
              dataIndex: 'description',
              render: (_, record, index) => (
                <Input
                  value={record.description}
                  onChange={(e) =>
                    updateLine(index, 'description', e.target.value)
                  }
                />
              ),
            },
            {
              title: t('form.invoiceLinesQuantity', 'Quantity'),
              dataIndex: 'quantity',
              render: (_, record, index) => (
                <InputNumber
                  value={record.quantity}
                  onChange={(value) =>
                    value && updateLine(index, 'quantity', value)
                  }
                />
              ),
            },
            {
              title: t('form.invoiceLinesPrice', 'Price'),
              dataIndex: 'price',
              render: (_, record, index) => (
                <InputNumber
                  value={record.price}
                  onChange={(value) =>
                    value && updateLine(index, 'price', value)
                  }
                />
              ),
            },
            {
              title: t('form.invoiceLinesTotal', 'Total'),
              dataIndex: 'total',
              render: (total) => total.toFixed(2),
            },
            {
              title: t('form.actions', 'Actions'),
              render: (_, __, index) => (
                <Button danger onClick={() => removeLine(index)}>
                  {t('form.remove', 'Remove')}
                </Button>
              ),
            },
          ]}
          pagination={false}
          rowKey={(record) =>
            record.id ? record.id.toString() : (record.tempId as string)
          }
        />
        <Button type='dashed' onClick={addLine} block>
          {t('form.addLine', 'Add Line')}
        </Button>
        <Button type='primary' htmlType='submit'>
          {t('form.saveInvoice', 'Save Invoice')}
        </Button>
      </Form>
    </div>
  );
};

export default CreateInvoicePage;
