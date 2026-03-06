(() => {
  const menuToggle = document.querySelector(".menu-toggle");
  const siteMenu = document.getElementById("siteMenu");
  if (menuToggle && siteMenu) {
    menuToggle.addEventListener("click", () => {
      const isOpen = siteMenu.classList.toggle("open");
      menuToggle.setAttribute("aria-expanded", isOpen ? "true" : "false");
    });
  }

  const welcomePopup = document.getElementById("welcomePopup");
  const welcomePopupDismiss = document.getElementById("welcomePopupDismiss");
  const welcomePopupSend = document.getElementById("welcomePopupSend");
  const welcomeStorageKey = "babadovip_welcome_popup_v1";
  const hasDismissedWelcome = () => {
    try {
      return window.localStorage.getItem(welcomeStorageKey) === "1";
    } catch (error) {
      return false;
    }
  };
  const markWelcomeDismissed = () => {
    try {
      window.localStorage.setItem(welcomeStorageKey, "1");
    } catch (error) {
      // no-op: storage can be blocked by browser privacy settings
    }
  };
  const closeWelcomePopup = () => {
    if (!welcomePopup) return;
    markWelcomeDismissed();
    welcomePopup.classList.remove("show");
    welcomePopup.hidden = true;
  };
  const openWelcomePopup = () => {
    if (!welcomePopup || !welcomePopupDismiss || !welcomePopupSend) return;
    if (hasDismissedWelcome()) return;
    welcomePopup.hidden = false;
    welcomePopup.classList.add("show");
  };
  if (welcomePopupDismiss) {
    welcomePopupDismiss.addEventListener("click", closeWelcomePopup);
  }
  if (welcomePopupSend) {
    welcomePopupSend.addEventListener("click", () => {
      markWelcomeDismissed();
    });
  }

  const lgpdPopup = document.getElementById("lgpdPopup");
  const lgpdPopupAccept = document.getElementById("lgpdPopupAccept");
  if (lgpdPopup && lgpdPopupAccept) {
    const lgpdStorageKey = "babadovip_lgpd_notice_v1";
    const hasAcceptedLgpd = () => {
      try {
        return window.localStorage.getItem(lgpdStorageKey) === "1";
      } catch (error) {
        return false;
      }
    };
    const markLgpdAccepted = () => {
      try {
        window.localStorage.setItem(lgpdStorageKey, "1");
      } catch (error) {
        // no-op: storage can be blocked by browser privacy settings
      }
    };
    const closeLgpdPopup = () => {
      markLgpdAccepted();
      lgpdPopup.classList.remove("show");
      lgpdPopup.hidden = true;
      openWelcomePopup();
    };

    if (!hasAcceptedLgpd()) {
      lgpdPopup.hidden = false;
      lgpdPopup.classList.add("show");
    } else {
      openWelcomePopup();
    }

    lgpdPopupAccept.addEventListener("click", closeLgpdPopup);
    document.addEventListener("keydown", (event) => {
      if (event.key === "Escape" && !lgpdPopup.hidden) {
        closeLgpdPopup();
      } else if (event.key === "Escape" && welcomePopup && !welcomePopup.hidden) {
        closeWelcomePopup();
      }
    });
  } else {
    openWelcomePopup();
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
    const previewTargetId = input.getAttribute("data-preview-target") || "";
    const previewTarget = previewTargetId ? document.getElementById(previewTargetId) : null;
    let previewUrls = [];
    let selectedFiles = [];

    const clearPreviewUrls = () => {
      previewUrls.forEach((url) => URL.revokeObjectURL(url));
      previewUrls = [];
    };

    const syncSelectedFiles = () => {
      const dt = new DataTransfer();
      selectedFiles.forEach((file) => dt.items.add(file));
      input.files = dt.files;
    };

    const renderSimplePreview = () => {
      if (!previewTarget) return;
      clearPreviewUrls();
      previewTarget.innerHTML = "";

      if (!selectedFiles.length) {
        return;
      }

      selectedFiles.forEach((file, idx) => {
        const item = document.createElement("article");
        item.className = "upload-preview-item";

        if ((file.type || "").startsWith("image/")) {
          const thumb = document.createElement("img");
          const url = URL.createObjectURL(file);
          previewUrls.push(url);
          thumb.src = url;
          thumb.alt = `Foto ${idx + 1}`;
          item.appendChild(thumb);
        } else {
          const placeholder = document.createElement("div");
          placeholder.className = "upload-preview-placeholder";
          placeholder.textContent = "Arquivo";
          item.appendChild(placeholder);
        }

        const info = document.createElement("div");
        info.className = "upload-preview-info";
        const name = document.createElement("strong");
        name.textContent = `${idx + 1}. ${file.name}`;
        const size = document.createElement("small");
        size.textContent = `${Math.max(1, Math.round(file.size / 1024))} KB`;
        info.appendChild(name);
        info.appendChild(size);
        const removeBtn = document.createElement("button");
        removeBtn.type = "button";
        removeBtn.className = "btn-danger upload-preview-remove";
        removeBtn.dataset.removeIndex = String(idx);
        removeBtn.textContent = "Remover";
        info.appendChild(removeBtn);
        item.appendChild(info);
        previewTarget.appendChild(item);
      });
    };

    if (previewTarget) {
      previewTarget.addEventListener("click", (event) => {
        const target = event.target;
        if (!(target instanceof HTMLElement)) return;
        const removeBtn = target.closest("button[data-remove-index]");
        if (!(removeBtn instanceof HTMLButtonElement)) return;
        const removeIndex = parseInt(removeBtn.dataset.removeIndex || "", 10);
        if (Number.isNaN(removeIndex) || !selectedFiles[removeIndex]) return;
        selectedFiles.splice(removeIndex, 1);
        syncSelectedFiles();
        renderSimplePreview();
      });
    }

    input.addEventListener("change", () => {
      const max = parseInt(input.getAttribute("data-max-files") || "20", 10);
      const maxFileSizeBytes = parseInt(input.getAttribute("data-max-size-bytes") || String(5 * 1024 * 1024), 10);
      const pickedFiles = Array.from(input.files || []);
      if (!pickedFiles.length) {
        return;
      }

      const allowedBySize = [];
      let oversizedCount = 0;
      pickedFiles.forEach((file) => {
        if (Number.isFinite(maxFileSizeBytes) && maxFileSizeBytes > 0 && file.size > maxFileSizeBytes) {
          oversizedCount += 1;
          return;
        }
        allowedBySize.push(file);
      });
      if (oversizedCount > 0) {
        const sizeMb = (maxFileSizeBytes / (1024 * 1024)).toFixed(0);
        alert(`${oversizedCount} arquivo(s) acima de ${sizeMb} MB não foram adicionados.`);
      }

      const existingKeys = new Set(
        selectedFiles.map((file) => `${file.name}|${file.size}|${file.lastModified}|${file.type || ""}`)
      );
      const uniquePicked = allowedBySize.filter((file) => {
        const key = `${file.name}|${file.size}|${file.lastModified}|${file.type || ""}`;
        if (existingKeys.has(key)) {
          return false;
        }
        existingKeys.add(key);
        return true;
      });

      const availableSlots = Math.max(0, max - selectedFiles.length);
      if (availableSlots <= 0) {
        alert(`Limite de ${max} arquivos.`);
        syncSelectedFiles();
        renderSimplePreview();
        return;
      }

      const filesToAdd = uniquePicked.slice(0, availableSlots);
      if (filesToAdd.length < uniquePicked.length) {
        alert(`Limite de ${max} arquivos. Apenas ${filesToAdd.length} arquivo(s) adicionado(s).`);
      }

      selectedFiles = selectedFiles.concat(filesToAdd);
      syncSelectedFiles();
      renderSimplePreview();
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

  const shareButtons = document.querySelectorAll("[data-share-url]");
  if (shareButtons.length) {
    const copyText = async (text) => {
      if (navigator.clipboard && window.isSecureContext) {
        await navigator.clipboard.writeText(text);
        return;
      }
      const textarea = document.createElement("textarea");
      textarea.value = text;
      textarea.setAttribute("readonly", "readonly");
      textarea.style.position = "absolute";
      textarea.style.left = "-9999px";
      document.body.appendChild(textarea);
      textarea.select();
      document.execCommand("copy");
      document.body.removeChild(textarea);
    };

    shareButtons.forEach((btn) => {
      btn.addEventListener("click", async () => {
        const shareUrl = btn.getAttribute("data-share-url") || "";
        const shareTitle = btn.getAttribute("data-share-title") || document.title;
        const feedbackId = btn.getAttribute("data-share-feedback") || "";
        const feedback = feedbackId ? document.getElementById(feedbackId) : null;
        const setFeedback = (message, isError = false) => {
          if (!feedback) return;
          feedback.textContent = message;
          feedback.style.color = isError ? "#ffb4c8" : "#9fe3ac";
          const existingTimer = Number(feedback.dataset.timerId || "0");
          if (existingTimer > 0) {
            window.clearTimeout(existingTimer);
          }
          const timerId = window.setTimeout(() => {
            feedback.textContent = "";
            feedback.dataset.timerId = "0";
          }, 2800);
          feedback.dataset.timerId = String(timerId);
        };

        if (!shareUrl) return;

        if (typeof navigator.share === "function") {
          try {
            await navigator.share({
              title: shareTitle,
              text: shareTitle,
              url: shareUrl,
            });
            setFeedback("Compartilhado.");
            return;
          } catch (error) {
            if (error && error.name === "AbortError") {
              return;
            }
          }
        }

        try {
          await copyText(shareUrl);
          setFeedback("Link copiado!");
        } catch (error) {
          setFeedback("Nao foi possivel copiar o link.", true);
        }
      });
    });
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
