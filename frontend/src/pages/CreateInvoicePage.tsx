import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import { v4 as uuidv4 } from 'uuid';
import moment from 'moment';
import { useTranslation } from 'react-i18next';
import { useInvoice } from '../contexts/InvoiceContext';
import { InvoicePayload, EditableInvoiceLine } from '../interfaces';
import { Button, Label, TextInput, Select, Table } from 'flowbite-react';

const CreateInvoicePage: React.FC = () => {
  const [invoiceNumber, setInvoiceNumber] = useState('');
  const [invoiceDate, setInvoiceDate] = useState(moment().format('YYYY-MM-DD'));
  const [toName, setToName] = useState('');
  const [city, setCity] = useState('');
  const [invoiceType, setInvoiceType] = useState<number>(1);
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

  const onSubmit = async (event: React.FormEvent) => {
    event.preventDefault();
    const payload: InvoicePayload = {
      invoice_number: invoiceNumber,
      invoice_date: invoiceDate,
      to_name: toName,
      city,
      invoice_type: invoiceType,
      lines,
    };

    try {
      await createInvoice(payload);
      navigate('/invoices');
    } catch (error) {
      console.error(error);
    }
  };

  return (
    <div className='max-w-3xl mx-auto p-6 bg-white rounded-lg shadow'>
      <h1 className='text-2xl font-bold mb-4'>
        {t('form.createInvoice', 'Create Invoice')}
      </h1>
      <form onSubmit={onSubmit} className='space-y-4'>
        <div>
          <Label htmlFor='invoiceNumber'>
            {t('form.invoiceNumber', 'Invoice Number')}
          </Label>
          <TextInput
            id='invoiceNumber'
            value={invoiceNumber}
            onChange={(e) => setInvoiceNumber(e.target.value)}
            required
          />
        </div>
        <div>
          <Label htmlFor='invoiceDate'>
            {t('form.invoiceDate', 'Invoice Date')}
          </Label>
          <TextInput
            id='invoiceDate'
            type='date'
            value={invoiceDate}
            onChange={(e) => setInvoiceDate(e.target.value)}
            required
          />
        </div>
        <div>
          <Label htmlFor='toName'>{t('form.toName', 'To Name')}</Label>
          <TextInput
            id='toName'
            value={toName}
            onChange={(e) => setToName(e.target.value)}
          />
        </div>
        <div>
          <Label htmlFor='city'>{t('form.city', 'City')}</Label>
          <TextInput
            id='city'
            value={city}
            onChange={(e) => setCity(e.target.value)}
          />
        </div>
        <div>
          <Label htmlFor='invoiceType'>{t('form.invoiceType', 'Type')}</Label>
          <Select
            id='invoiceType'
            value={invoiceType}
            onChange={(e) => setInvoiceType(Number(e.target.value))}
          >
            <option value={1}>{t('invoiceType.1', 'Invoice')}</option>
            <option value={2}>{t('invoiceType.2', 'Proforma Invoice')}</option>
            <option value={3}>{t('invoiceType.3', 'Offer')}</option>
          </Select>
        </div>

        <Table className='mt-4'>
          <Table.Head>
            <Table.HeadCell>
              {t('form.invoiceLinesDescription', 'Description')}
            </Table.HeadCell>
            <Table.HeadCell>
              {t('form.invoiceLinesQuantity', 'Quantity')}
            </Table.HeadCell>
            <Table.HeadCell>
              {t('form.invoiceLinesPrice', 'Price')}
            </Table.HeadCell>
            <Table.HeadCell>
              {t('form.invoiceLinesTotal', 'Total')}
            </Table.HeadCell>
            <Table.HeadCell>{t('form.actions', 'Actions')}</Table.HeadCell>
          </Table.Head>
          <Table.Body>
            {lines.map((line, index) => (
              <Table.Row key={line.tempId || line.id}>
                <Table.Cell>
                  <TextInput
                    value={line.description}
                    onChange={(e) =>
                      updateLine(index, 'description', e.target.value)
                    }
                  />
                </Table.Cell>
                <Table.Cell>
                  <TextInput
                    type='number'
                    value={line.quantity}
                    onChange={(e) =>
                      updateLine(index, 'quantity', Number(e.target.value))
                    }
                  />
                </Table.Cell>
                <Table.Cell>
                  <TextInput
                    type='number'
                    value={line.price}
                    onChange={(e) =>
                      updateLine(index, 'price', Number(e.target.value))
                    }
                  />
                </Table.Cell>
                <Table.Cell>{line.total.toFixed(2)}</Table.Cell>
                <Table.Cell>
                  <Button color='red' onClick={() => removeLine(index)}>
                    {t('form.remove', 'Remove')}
                  </Button>
                </Table.Cell>
              </Table.Row>
            ))}
          </Table.Body>
        </Table>

        <Button type='button' color='blue' onClick={addLine} className='mt-4'>
          {t('form.addLine', 'Add Line')}
        </Button>
        <Button type='submit' color='green' className='w-full mt-4'>
          {t('form.saveInvoice', 'Save Invoice')}
        </Button>
      </form>
    </div>
  );
};

export default CreateInvoicePage;
