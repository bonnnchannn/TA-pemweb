document.addEventListener("DOMContentLoaded", function () {
  // ZOOM IMAGE KETIKA DIKLIK
  const images = document.querySelectorAll(".card-img-top");
  images.forEach((img) => {
    img.addEventListener("click", function () {
      this.classList.toggle("zoomed");
    });
  });

  // MODAL IMAGE SAAT KLIK DETAIL
  const detailButtons = document.querySelectorAll(".btn-primary");
  const modalImage = document.getElementById("modalImage");
  const modalEl = document.getElementById("imageModal");

  if (modalImage && modalEl) {
    detailButtons.forEach((button) => {
      button.addEventListener("click", function (e) {
        e.preventDefault();
        const card = this.closest(".card");
        const imgSrc = card.querySelector("img").getAttribute("src");
        modalImage.setAttribute("src", imgSrc);
        const modal = new bootstrap.Modal(modalEl);
        modal.show();
      });
    });
  }

  // FITUR KERANJANG
  const beliButtons = document.querySelectorAll(".btn-beli");
  if (beliButtons.length > 0) {
    beliButtons.forEach((button) => {
      button.removeEventListener("click", handleBeliClick); // Pastikan tidak dobel
      button.addEventListener("click", handleBeliClick);
    });
  }

  // FORM KONTAK
  const form = document.getElementById("contactForm");
  if (form) {
    form.addEventListener("submit", function (e) {
      e.preventDefault();

      const nama = document.getElementById("nama").value;
      const email = document.getElementById("email").value;
      const pesan = document.getElementById("pesan").value;

      const kontak = {
        nama,
        email,
        pesan,
        waktu: new Date().toLocaleString(),
      };

      let daftarKontak = JSON.parse(localStorage.getItem("daftarKontak")) || [];
      daftarKontak.push(kontak);
      localStorage.setItem("daftarKontak", JSON.stringify(daftarKontak));

      Swal.fire({
        icon: 'success',
        title: 'Pesan Terkirim!',
        text: 'Terima kasih telah menghubungi kami.',
        confirmButtonColor: '#3085d6',
      });

      form.reset();
    });
  }

  // TAMPILKAN DATA KONTAK
  const daftarKontakEl = document.getElementById("daftarKontak");
  if (daftarKontakEl) {
    const dataKontak = JSON.parse(localStorage.getItem("daftarKontak")) || [];

    if (dataKontak.length === 0) {
      daftarKontakEl.innerHTML = `<div class="alert alert-info">Belum ada pesan masuk.</div>`;
    } else {
      dataKontak.forEach((kontak) => {
        const kontakHTML = `
          <div class="list-group-item">
            <h5>${kontak.nama} <small class="text-muted">(${kontak.email})</small></h5>
            <p>${kontak.pesan}</p>
            <small class="text-muted">${kontak.waktu}</small>
          </div>
        `;
        daftarKontakEl.innerHTML += kontakHTML;
      });
    }
  }

  updateCartCount(); // Tampilkan jumlah item saat halaman dimuat
});

// HANDLE KLIK TOMBOL BELI
function handleBeliClick(e) {
  e.preventDefault();
  const nama = this.getAttribute("data-nama");
  const harga = parseInt(this.getAttribute("data-harga"));
  const gambar = this.getAttribute("data-gambar");

  const produk = { nama, harga, gambar, jumlah: 1 };
  let keranjang = JSON.parse(localStorage.getItem("keranjang")) || [];

  const existing = keranjang.find((item) => item.nama === nama);
  if (existing) {
    existing.jumlah += 1;
  } else {
    keranjang.push(produk);
  }

  localStorage.setItem("keranjang", JSON.stringify(keranjang));
  updateCartCount(); // ✅ Update badge keranjang

  Swal.fire({
    toast: true,
    position: "top-end",
    icon: "success",
    title: `${nama} ditambahkan ke keranjang`,
    showConfirmButton: false,
    timer: 1500,
    timerProgressBar: true,
  });
}

// CHECKOUT FUNCTION
window.checkout = function () {
  let keranjang = JSON.parse(localStorage.getItem("keranjang")) || [];

  if (keranjang.length === 0) {
    Swal.fire({
      icon: "warning",
      title: "Oops...",
      text: "Keranjang kamu masih kosong!",
    });
    return;
  }

  Swal.fire({
    title: "Checkout Berhasil!",
    text: "Terima kasih! Pesanan Anda diproses.",
    icon: "success",
    confirmButtonText: "OK",
  }).then(() => {
    localStorage.removeItem("keranjang");
    updateCartCount(); // Reset jumlah item
    window.location.reload();
  });
};

// TAMPILKAN JUMLAH ITEM DI ICON KERANJANG
function updateCartCount() {
  const cartBadge = document.getElementById("cartCount");
  let keranjang = JSON.parse(localStorage.getItem("keranjang")) || [];
  let totalItem = keranjang.reduce((total, item) => total + item.jumlah, 0);

  if (cartBadge) {
    cartBadge.textContent = totalItem;
    cartBadge.style.display = totalItem > 0 ? "inline-block" : "none";
  }
}

// Tambahkan ke index.js
window.addEventListener('scroll', function() {
  if (window.scrollY > 50) {
    document.querySelector('.navbar').classList.add('scrolled');
  } else {
    document.querySelector('.navbar').classList.remove('scrolled');
  }
});

document.getElementById('checkoutForm').addEventListener('submit', function(e) {
  e.preventDefault();
  
  const keranjang = JSON.parse(localStorage.getItem('keranjang')) || [];
  const customerName = document.getElementById('customerName').value;
  const customerEmail = document.getElementById('customerEmail').value;
  const customerPhone = document.getElementById('customerPhone').value;
  
  // Validasi data pelanggan
  if (!customerName.trim()) {
    Swal.fire({
      icon: "error",
      title: "Error",
      text: "Nama lengkap harus diisi!",
    });
    return;
  }

  if (keranjang.length === 0) {
    Swal.fire({
      icon: "warning",
      title: "Oops...",
      text: "Keranjang masih kosong!",
    });
    return;
  }

  // Hitung total harga
  let totalAmount = 0;
  keranjang.forEach(item => {
    totalAmount += item.harga * item.jumlah;
  });

  // Kirim data checkout ke backend
  setTimeout(() => {
    fetch('checkout.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        items: keranjang,
        customer_name: customerName,
        customer_email: customerEmail,
        customer_phone: customerPhone,
        total_amount: totalAmount,
      })
    })
    .then(response => response.json())
    .then(data => {
      Swal.close();
      
      if (data.success) {
        Swal.fire({
          title: "Checkout Berhasil!",
          text: `Terima kasih ${customerName}! Pesanan Anda dengan ID #${data.order_id} sedang diproses.`,
          icon: "success",
          confirmButtonText: "OK",
          confirmButtonColor: "#4f46e5"
        }).then(() => {
          localStorage.removeItem('keranjang');
          renderKeranjang();
          hideCustomerForm();
          document.getElementById('checkoutForm').reset();
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "Error",
          text: data.message || "Terjadi kesalahan pada server",
        });
      }
    })
    .catch(error => {
      Swal.close();
      console.error('Error:', error);
      Swal.fire({
        icon: "error",
        title: "Error",
        text: "Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.",
      });
    });
  }, 1000); // Simulasi waktu proses
});
