import axios from 'axios';
import Swal from 'sweetalert2';

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

  const handleMessage = (message, type = 'error') => {
    if ( type === 'error' ) {
      Swal.fire({
        icon: 'error',
        title: 'Ops...',
        text: message
      });
    } else {
      Swal.fire({
        icon: 'success',
        title: message,
        showConfirmButton: false,
        timer: 1500
      });
    }

  }

  const request = (formData) => {
    axios.post( window.waclVars.ajaxUrl, formData).then((response) => {
      document.body.classList.remove('is-loading');
      const { data } = response.data;

      if (!response.data.success) {
        handleMessage(data.message);
        return;
      }

      handleMessage(data.message, 'success');
    });
  }

  checkboxes.forEach(checkbox => {
    checkbox.addEventListener("change", handleChange);
  })
});
