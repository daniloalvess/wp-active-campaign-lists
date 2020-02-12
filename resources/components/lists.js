import axios from 'axios';

document.addEventListener("DOMContentLoaded", function() {
  const component = document.querySelector('[data-component=lists]');
  const nonce = component.querySelector('#_wpnonce').value;
  const checkboxes = component.querySelectorAll('[data-element=checkbox]');

  const handleChange = (event) => {
    const element = event.target;
    const form = new FormData();

    document.body.classList.add('is-loading');

    form.append('action', 'e36f520fa');
    form.append('nonce', nonce);
    form.append('contact_id', component.dataset.contact);
    form.append('list_id', element.dataset.list);
    form.append('status', element.checked ? 1 : 2);

    request(form, element);
  }

  const request = (formData) => {
    axios.post( window.waclVars.ajaxUrl, formData).then((response) => {
      document.body.classList.remove('is-loading');
      if (!response.data.success) {
        const { data } = response.data;
        alert(data.message);
      }
    });
  }

  checkboxes.forEach(checkbox => {
    checkbox.addEventListener("change", handleChange);
  })
});
