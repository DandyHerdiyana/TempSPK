document.addEventListener("DOMContentLoaded", () => {
  // Form steps navigation
  const form = document.getElementById('registrationForm');
  const steps = Array.from(document.querySelectorAll('.form-step'));
  const nextBtns = Array.from(document.querySelectorAll('.next-btn'));
  const prevBtns = Array.from(document.querySelectorAll('.prev-btn'));
  const progressSteps = Array.from(document.querySelectorAll('.progress-step'));
  
  let currentStep = 0;

  // Initialize form
  initForm();

  function initForm() {
    // Populate year select
    const yearSelect = document.getElementById('tahun_lulus');
    const currentYear = new Date().getFullYear();
    for (let y = currentYear; y >= currentYear - 10; y--) {
      const option = document.createElement('option');
      option.value = y;
      option.textContent = y;
      yearSelect.appendChild(option);
    }

    // Load provinces
    loadProvinces();

    // Handle KIP/Prestasi documents visibility
    const jalurSelect = document.getElementById('jalur_pendaftaran');
    jalurSelect.addEventListener('change', toggleSpecialDocuments);
  }

  function toggleSpecialDocuments() {
    const jalur = this.value;
    document.querySelectorAll('.kip-only, .prestasi-only').forEach(el => {
      el.style.display = 'none';
    });

    if (jalur === 'KIP') {
      document.querySelectorAll('.kip-only').forEach(el => {
        el.style.display = 'block';
      });
    } else if (jalur === 'Prestasi') {
      document.querySelectorAll('.prestasi-only').forEach(el => {
        el.style.display = 'block';
      });
    }
  }

  async function loadProvinces() {
    const provSelect = document.getElementById('provinsi');
    const kabSelect = document.getElementById('kabupaten_kota');

    try {
      const response = await fetch('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json');
      const provinces = await response.json();
      
      provinces.forEach(province => {
        const option = document.createElement('option');
        option.value = province.name;
        option.textContent = province.name;
        option.dataset.id = province.id;
        provSelect.appendChild(option);
      });

      provSelect.addEventListener('change', async () => {
        const selectedOption = provSelect.options[provSelect.selectedIndex];
        const provinceId = selectedOption.dataset.id;
        
        if (!provinceId) return;
        
        kabSelect.innerHTML = '<option value="">Pilih Kabupaten/Kota</option>';
        const kabResponse = await fetch(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${provinceId}.json`);
        const kabupatens = await kabResponse.json();
        
        kabupatens.forEach(kabupaten => {
          const option = document.createElement('option');
          option.value = kabupaten.name;
          option.textContent = kabupaten.name;
          kabSelect.appendChild(option);
        });
      });
    } catch (error) {
      console.error("Error loading provinces:", error);
    }
  }

  // Navigation functions
  function showStep(stepIndex) {
    steps.forEach((step, index) => {
      step.classList.toggle('active', index === stepIndex);
    });
    
    progressSteps.forEach((step, index) => {
      step.classList.toggle('active', index <= stepIndex);
    });
    
    currentStep = stepIndex;
  }

  nextBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const currentStepForm = steps[currentStep];
      const inputs = currentStepForm.querySelectorAll('input, select, textarea[required]');
      let isValid = true;
      
      inputs.forEach(input => {
        if (!input.value.trim()) {
          input.style.borderColor = 'var(--warning)';
          isValid = false;
        } else {
          input.style.borderColor = '';
        }
      });
      
      if (isValid) {
        showStep(currentStep + 1);
      } else {
        Swal.fire({
          icon: 'warning',
          title: 'Data Belum Lengkap',
          text: 'Harap isi semua field yang wajib diisi',
        });
      }
    });
  });

  prevBtns.forEach(btn => {
    btn.addEventListener('click', () => showStep(currentStep - 1));
  });

  // Form submission
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    const formData = new FormData(form);
    
    try {
      const response = await fetch('process-registration.php', {
        method: 'POST',
        body: formData
      });
      
      const result = await response.json();
      
      if (response.ok) {
        Swal.fire({
          icon: 'success',
          title: 'Pendaftaran Berhasil!',
          text: result.message || 'Data Anda telah berhasil dikirim',
        }).then(() => {
          window.location.href = 'success.html';
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Gagal Mendaftar',
          text: result.message || 'Terjadi kesalahan saat mengirim data',
        });
      }
    } catch (error) {
      console.error('Error:', error);
      Swal.fire({
        icon: 'error',
        title: 'Kesalahan Jaringan',
        text: 'Tidak dapat terhubung ke server',
      });
    }
  });
});