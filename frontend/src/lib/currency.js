export const formatCurrency = (amount, currency = 'ZAR', locale = 'en-ZA') => {
    if (amount === undefined || amount === null || isNaN(amount)) {
        return 'R 0,00';
    }

    const numericAmount = Number(amount);
    const EXCHANGE_RATE = 19; 
    const convertedAmount = numericAmount * EXCHANGE_RATE;

    return new Intl.NumberFormat(locale, {
        style: 'currency',
        currency: currency,
    }).format(convertedAmount);
};