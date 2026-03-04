(() => {
  const menuToggle = document.querySelector(".menu-toggle");
  const siteMenu = document.getElementById("siteMenu");
  if (menuToggle && siteMenu) {
    menuToggle.addEventListener("click", () => {
      const isOpen = siteMenu.classList.toggle("open");
      menuToggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
    });
  }

  const lightbox = document.getElementById("lightbox");
  const galleryData = window.BABADOVIP_GALLERY || [];
  if (lightbox && galleryData.length) {
    const img = document.getElementById("lightboxImage");
    const cap = document.getElementById("lightboxCaption");
    let idx = 0;
    const open = (i) => {
      idx = (i + galleryData.length) % galleryData.length;
      img.src = galleryData[idx].src;
      cap.textContent = galleryData[idx].caption || "";
      lightbox.classList.add("show");
    };
    document.querySelectorAll(".lightbox-trigger").forEach((el) => {
      el.addEventListener("click", () => open(parseInt(el.dataset.index || "0", 10)));
    });
    lightbox.querySelector(".lightbox-close")?.addEventListener("click", () => lightbox.classList.remove("show"));
    lightbox.querySelector(".lightbox-prev")?.addEventListener("click", () => open(idx - 1));
    lightbox.querySelector(".lightbox-next")?.addEventListener("click", () => open(idx + 1));
    let startX = 0;
    lightbox.addEventListener("touchstart", (e) => { startX = e.touches[0].clientX; }, { passive: true });
    lightbox.addEventListener("touchend", (e) => {
      const delta = e.changedTouches[0].clientX - startX;
      if (Math.abs(delta) > 40) open(delta < 0 ? idx + 1 : idx - 1);
    });
  }

  document.querySelectorAll("input[type=file][data-max-files]").forEach((input) => {
    if (input.getAttribute("data-gallery-managed") === "1") return;
    input.addEventListener("change", () => {
      const max = parseInt(input.getAttribute("data-max-files") || "20", 10);
      if ((input.files || []).length > max) {
        alert(`Limite de ${max} arquivos.`);
        input.value = "";
      }
    });
  });

  const coverInput = document.querySelector('input[type="file"][name="imagem_capa"]');
  const coverCard = document.getElementById("coverPreviewCard");
  const coverImage = document.getElementById("coverPreviewImage");
  const removeCoverCheckbox = document.getElementById("removerImagemCapa");
  if (coverInput && coverCard && coverImage) {
    const syncCoverPreview = () => {
      const file = (coverInput.files || [])[0];
      if (file) {
        coverImage.src = URL.createObjectURL(file);
        coverCard.classList.remove("is-hidden");
        return;
      }
      if (removeCoverCheckbox && removeCoverCheckbox.checked) {
        coverImage.src = "";
        coverCard.classList.add("is-hidden");
        return;
      }
      const originalSrc = coverCard.getAttribute("data-original-src") || "";
      if (originalSrc !== "") {
        coverImage.src = originalSrc;
        coverCard.classList.remove("is-hidden");
      } else {
        coverImage.src = "";
        coverCard.classList.add("is-hidden");
      }
    };

    coverInput.addEventListener("change", () => {
      if ((coverInput.files || []).length > 0 && removeCoverCheckbox) {
        removeCoverCheckbox.checked = false;
      }
      syncCoverPreview();
    });

    if (removeCoverCheckbox) {
      removeCoverCheckbox.addEventListener("change", () => {
        if (removeCoverCheckbox.checked) {
          coverInput.value = "";
        }
        syncCoverPreview();
      });
    }
  }

  const galleryList = document.getElementById("galleryList");
  if (galleryList) {
    const uploadInput = document.querySelector('input[type="file"][name="fotos[]"][data-gallery-managed="1"]');
    let dragEl = null;
    let pendingFiles = [];

    const updateOrders = () => {
      galleryList.querySelectorAll(".admin-gallery-item").forEach((item, i) => {
        const order = item.querySelector(".ordem-input");
        if (order) order.value = String(i);
      });
    };

    const syncUploadInput = () => {
      if (!uploadInput) return;
      const dt = new DataTransfer();
      pendingFiles.forEach((file) => dt.items.add(file));
      uploadInput.files = dt.files;
    };

    const renderUploadCards = () => {
      galleryList.querySelectorAll('.admin-gallery-item[data-gallery-kind="upload"]').forEach((item) => item.remove());
      pendingFiles.forEach((file, i) => {
        const item = document.createElement("div");
        item.className = "admin-gallery-item";
        item.draggable = true;
        item.dataset.galleryKind = "upload";
        item.dataset.uploadIndex = String(i);
        item.innerHTML = `
          <img src="${URL.createObjectURL(file)}" alt="">
          <input type="text" name="new_legendas[]" placeholder="Comentário da foto" maxlength="255">
          <button type="button" class="btn-danger remove-item">Remover</button>
        `;
        galleryList.appendChild(item);
      });
      updateOrders();
    };

    const syncPendingOrderFromDom = () => {
      const uploadItems = Array.from(galleryList.querySelectorAll('.admin-gallery-item[data-gallery-kind="upload"]'));
      if (!uploadItems.length || uploadItems.length !== pendingFiles.length) return;
      const reordered = [];
      uploadItems.forEach((item) => {
        const oldIndex = parseInt(item.dataset.uploadIndex || "", 10);
        if (!Number.isNaN(oldIndex) && pendingFiles[oldIndex]) {
          reordered.push(pendingFiles[oldIndex]);
        }
      });
      if (reordered.length === pendingFiles.length) {
        pendingFiles = reordered;
      }
      syncUploadInput();
      renderUploadCards();
    };

    galleryList.addEventListener("click", (e) => {
      const target = e.target;
      if (!(target instanceof HTMLElement) || !target.classList.contains("remove-item")) return;
      const item = target.closest(".admin-gallery-item");
      if (!item) return;
      if (item.dataset.galleryKind === "upload") {
        const removeIndex = parseInt(item.dataset.uploadIndex || "", 10);
        if (!Number.isNaN(removeIndex)) {
          pendingFiles.splice(removeIndex, 1);
          syncUploadInput();
          renderUploadCards();
        }
        return;
      }
      item.remove();
      updateOrders();
    });

    galleryList.addEventListener("dragstart", (e) => {
      const target = e.target;
      if (!(target instanceof HTMLElement)) return;
      const item = target.closest(".admin-gallery-item");
      if (!item) return;
      dragEl = item;
      item.style.opacity = ".5";
    });

    galleryList.addEventListener("dragend", (e) => {
      const target = e.target;
      if (!(target instanceof HTMLElement)) return;
      const item = target.closest(".admin-gallery-item");
      if (!item) return;
      item.style.opacity = "1";
      syncPendingOrderFromDom();
      updateOrders();
    });

    galleryList.addEventListener("dragover", (e) => e.preventDefault());
    galleryList.addEventListener("drop", (e) => {
      e.preventDefault();
      const target = e.target;
      if (!(target instanceof HTMLElement)) return;
      const item = target.closest(".admin-gallery-item");
      if (!item || !dragEl || dragEl === item) return;
      const rect = item.getBoundingClientRect();
      const before = e.clientY < rect.top + rect.height / 2;
      item.parentNode.insertBefore(dragEl, before ? item : item.nextSibling);
    });

    if (uploadInput) {
      uploadInput.addEventListener("change", () => {
        const selected = Array.from(uploadInput.files || []);
        if (!selected.length) return;
        const max = parseInt(uploadInput.getAttribute("data-max-files") || "20", 10);
        if (pendingFiles.length + selected.length > max) {
          alert(`Limite de ${max} arquivos.`);
          syncUploadInput();
          return;
        }
        pendingFiles = pendingFiles.concat(selected);
        syncUploadInput();
        renderUploadCards();
      });
    }

    updateOrders();
  }

  const commentField = document.querySelector('textarea[name="mensagem"]');
  if (commentField) {
    document.querySelectorAll("[data-emoji-insert]").forEach((btn) => {
      btn.addEventListener("click", () => {
        const emoji = btn.getAttribute("data-emoji-insert") || "";
        const start = commentField.selectionStart ?? commentField.value.length;
        const end = commentField.selectionEnd ?? commentField.value.length;
        const before = commentField.value.slice(0, start);
        const after = commentField.value.slice(end);
        commentField.value = `${before}${emoji}${after}`;
        const nextPos = start + emoji.length;
        commentField.focus();
        commentField.setSelectionRange(nextPos, nextPos);
      });
    });
  }
})();
