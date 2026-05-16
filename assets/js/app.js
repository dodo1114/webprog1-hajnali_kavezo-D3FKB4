document.addEventListener('DOMContentLoaded', () => {
  const contactForm = document.querySelector('[data-contact-form]');
  if (contactForm) {
    contactForm.addEventListener('submit', (event) => {
      const errors = [];
      const name = contactForm.elements.name.value.trim();
      const email = contactForm.elements.email.value.trim();
      const subject = contactForm.elements.subject.value.trim();
      const message = contactForm.elements.message.value.trim();

      if (name.length < 2) errors.push('A név legalább 2 karakter legyen.');
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) errors.push('Adj meg érvényes e-mail címet.');
      if (subject.length < 4) errors.push('A tárgy legalább 4 karakter legyen.');
      if (message.length < 10) errors.push('Az üzenet legalább 10 karakter legyen.');

      const box = contactForm.querySelector('.form-errors');
      box.innerHTML = '';
      if (errors.length > 0) {
        event.preventDefault();
        box.innerHTML = errors.map((error) => `<p>${error}</p>`).join('');
      }
    });
  }
});

