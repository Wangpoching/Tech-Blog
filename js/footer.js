// ② Footer 年份
const start = new Date('2021-01-01');
const now = new Date();
const years = ((now - start) / (1000 * 60 * 60 * 24 * 365.25)).toFixed(2);
document.getElementById('years').textContent = years;
