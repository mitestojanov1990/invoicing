// src/i18n.ts
import i18n from 'i18next';
import { initReactI18next } from 'react-i18next';

const resources = {
  en: {
    translation: {
      layout: {
        title: 'Invoicing',
        allInvoices: 'All Invoices',
        createInvoice: 'Create Invoice',
        signIn: 'Sign In',
        signOut: 'Sign Out',
      },
      table: {
        id: 'ID',
        invoiceNumber: 'Number',
        invoiceDate: 'Date',
        toName: 'To',
        city: 'City',
        invoiceType: 'Type',
        actions: 'Actions',
        edit: 'Edit',
        delete: 'Delete',
        pdf: 'PDF',
      },
      form: {
        signIn: 'Sign In',
        signInWithGoogle: 'Sign In with Google',
        signUp: 'Sign Up',
        noAccount: "Don't have an account?",
        haveAccount: 'Already have an account?',
        email: 'Email',
        emailRequired: 'Email is required',
        emailInvalid: 'Invalid email',
        password: 'Password',
        passwordRequired: 'Password is required',
        confirmPassword: 'Confirm Password',
        confirmPasswordRequired: 'Please confirm your password',
        name: 'Name',
        nameRequired: 'Name is required',
      },
      invoiceType: {
        '1': 'Invoice',
        '2': 'Proforma Invoice',
        '3': 'Offer',
      },
      messages: {
        fetchInvoicesFailed: 'Failed to fetch invoices',
        invoiceDeletedSuccess: 'Invoice deleted successfully.',
        invoiceDeleteFailed: 'Failed to delete invoice',
        signInSuccess: 'Signed in successfully',
        signInFailed: 'Sign in failed',
        signUpSuccess: 'Signed up successfully',
        signUpFailed: 'Sign up failed',
        passwordMismatch: 'Passwords do not match',
      },
    },
  },
  mk: {
    translation: {
      layout: {
        title: 'Фактурирање',
        allInvoices: 'Сите фактури',
        createInvoice: 'Создај фактура',
        signIn: 'Најави се',
        signOut: 'Одјави се',
      },
      table: {
        id: 'ИД',
        invoiceNumber: 'Број',
        invoiceDate: 'Датум',
        toName: 'До',
        city: 'Град',
        invoiceType: 'Тип на фактура',
        actions: 'Акции',
        edit: 'Уреди',
        delete: 'Избриши',
        pdf: 'PDF',
      },
      form: {
        signIn: 'Најави се',
        signInWithGoogle: 'Најави се со Google',
        signUp: 'Регистрирај се',
        noAccount: 'Немате профил?',
        haveAccount: 'Веќе имате профил?',
        email: 'Е-пошта',
        emailRequired: 'Е-поштата е задолжителна',
        emailInvalid: 'Невалидна е-пошта',
        password: 'Лозинка',
        passwordRequired: 'Лозинката е задолжителна',
        confirmPassword: 'Потврди лозинка',
        confirmPasswordRequired: 'Ве молиме потврдете ја лозинката',
        name: 'Име',
        nameRequired: 'Името е задолжително',
      },
      invoiceType: {
        '1': 'Фактура',
        '2': 'Профактура',
        '3': 'Понуда',
      },
      messages: {
        fetchInvoicesFailed: 'Не успеа да се вчитаат фактурите',
        invoiceDeletedSuccess: 'Фактурата е успешно избришана.',
        invoiceDeleteFailed: 'Не успеа да се избрише фактурата',
        signInSuccess: 'Успешно се најавивте',
        signInFailed: 'Не успеа да се најавите',
        signUpSuccess: 'Успешно се регистриравте',
        signUpFailed: 'Не успеа да се регистрирате',
        passwordMismatch: 'Лозинките не се совпаѓаат',
      },
    },
  },
};

i18n.use(initReactI18next).init({
  resources,
  lng: 'mk',
  fallbackLng: 'mk',
  interpolation: {
    escapeValue: false,
  },
});

export default i18n;
