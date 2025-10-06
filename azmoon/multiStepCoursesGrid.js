(function () {
  if (window._azmoonBoundV7) return;
  window._azmoonBoundV7 = true;

  /* ==== IDs مراحل ==== */
  const STEP1_IDS = ['konkoor-select', 'tiz6-select', 'tiz9-select', 'mid2-select'];
  const STEP2_IDS = ['tajrobi-select', 'riazi-select', 'ensani-select'];
  const ALL_SELECTABLE_IDS = [...new Set([...STEP1_IDS, ...STEP2_IDS])];

  /* ==== نگاشت ID → tag ==== */
  const AZ_TAGS = {
    step1: {
      'konkoor-select': 'konkoor',
      'tiz6-select': 'tiz6',
      'tiz9-select': 'tiz9',
      'mid2-select': 'mid2'
    },
    step2: {
      'tajrobi-select': 'tajrobi',
      'riazi-select': 'riazi',
      'ensani-select': 'ensani'
    }
  };

  /* ==== State ==== */
  const html = document.documentElement;
  let currentStep = 1;
  window.step1Choice = null;
  window.step2Choice = null;

  /* ==== Helpers ==== */
  const $ = id => document.getElementById(id);

  function setStep(n) {
    currentStep = n;
    html.dataset.azmoonStep = String(n);
    clearError();

    const stepsImg = document.getElementById('steps-img');
    if (stepsImg) {
      let bgUrl = '';
      if (n === 1) bgUrl = 'https://azmoon.tamland.ir/wp-content/uploads/2025/09/Frame-1171280432.png';
      if (n === 2) bgUrl = 'https://azmoon.tamland.ir/wp-content/uploads/2025/09/Frame-1171280432-1.png';
      if (n === 3) bgUrl = 'https://azmoon.tamland.ir/wp-content/uploads/2025/09/Frame-1171280432-2.png';

      stepsImg.style.backgroundImage = `url('${bgUrl}')`;
      stepsImg.style.backgroundSize = '294px auto';
      stepsImg.style.backgroundPosition = 'center center';
      stepsImg.style.backgroundRepeat = 'no-repeat';
    }
  }

  function enhanceSelectable(ids) {
    ids.forEach(id => {
      const el = $(id);
      if (el) {
        el.classList.add('azmoon-selectable');
        el.setAttribute('tabindex', '0');
        el.setAttribute('role', 'button');
        if (!el.hasAttribute('aria-pressed')) el.setAttribute('aria-pressed', 'false');
      }
    });
  }

  function clearSelection(ids) {
    ids.forEach(id => {
      const el = $(id);
      if (el) {
        el.classList.remove('is-selected');
        el.setAttribute('aria-pressed', 'false');
      }
    });
  }

  function selectEl(el) {
    el.classList.add('is-selected');
    el.setAttribute('aria-pressed', 'true');
  }

  function showError(msg, afterEl) {
    let box = $('azmoon-error');
    if (!box) {
      box = document.createElement('div');
      box.id = 'azmoon-error';
      (afterEl || document.body).insertAdjacentElement('afterend', box);
    }
    box.textContent = msg;
  }

  function clearError() {
    const box = $('azmoon-error');
    if (box) box.remove();
  }

  /* ==== مرحله ۳: رندر بر اساس برچسب‌ها ==== */
  function elHasTag(el, tag) {
    if (el.classList.contains('az-tag-' + tag)) return true;
    const data = el.getAttribute('data-az-tags');
    if (!data) return false;
    const tokens = data.split(/[,\s]+/).filter(Boolean);
    return tokens.includes(tag);
  }

  function renderStep3() {
    const s1 = AZ_TAGS.step1[window.step1Choice] || null;
    const s2 = window.step2Choice ? (AZ_TAGS.step2[window.step2Choice] || null) : null;

    const cards = document.querySelectorAll('#step3-container .az3-item');
    cards.forEach(card => {
      const parent = card.closest('.jet-listing-grid__item');
      if (!parent) return; // اگر والد پیدا نشد، رد شود

      const always = card.classList.contains('az-tag-all');
      const ok1 = s1 ? elHasTag(card, s1) : true;
      const ok2 = s2 ? elHasTag(card, s2) : true;

      if (always || (ok1 && ok2)) {
        parent.classList.remove('is-hidden');
      } else {
        parent.classList.add('is-hidden');
      }
    });
  }

  /* ==== رفتار انتخاب‌ها ==== */
  function handleStep1Select(target) {
    clearError();
    clearSelection(STEP1_IDS.filter(id => $(id)));
    selectEl(target);
    window.step1Choice = target.id;
    localStorage.setItem('azmoonChoiceStep1', window.step1Choice);
    html.dataset.azmoonChoiceStep1 = window.step1Choice;
  }

  function handleStep2Select(target) {
    clearError();
    clearSelection(STEP2_IDS.filter(id => $(id)));
    selectEl(target);
    window.step2Choice = target.id;
    localStorage.setItem('azmoonChoiceStep2', window.step2Choice);
    html.dataset.azmoonChoiceStep2 = window.step2Choice;
  }

  /* ==== دکمه‌ها ==== */
  function handleNextClick() {
    const nextBtn = $('az-next-btn');

    if (currentStep === 1) {
      if (!window.step1Choice) {
        showError('لطفاً یکی از گزینه‌های مرحلهٔ اول را انتخاب کنید.', nextBtn);
        return;
      }
      if (window.step1Choice === 'tiz6-select' || window.step1Choice === 'tiz9-select') {
        setStep(3);
        renderStep3();
        return;
      }
      setStep(2);
      window.step2Choice = null;
      clearSelection(STEP2_IDS.filter(id => $(id)));
      return;
    }

    if (currentStep === 2) {
      if (!window.step2Choice) {
        showError('لطفاً یکی از گزینه‌های مرحلهٔ دوم را انتخاب کنید.', nextBtn);
        return;
      }
      setStep(3);
      renderStep3();
      return;
    }
  }

  function handleBackClick() {
    if (currentStep === 2) {
      setStep(1);
      return;
    }
    if (currentStep === 3) {
      if (window.step1Choice === 'tiz6-select' || window.step1Choice === 'tiz9-select') {
        setStep(1);
      } else {
        setStep(2);
      }
      return;
    }
  }

  /* ==== Event Delegation ==== */
  const STEP1_SELECTOR = '[id="konkoor-select"],[id="tiz6-select"],[id="tiz9-select"],[id="mid2-select"]';
  const STEP2_SELECTOR = '[id="tajrobi-select"],[id="riazi-select"],[id="ensani-select"]';

  document.addEventListener('click', function(e) {
    const s1 = e.target.closest(STEP1_SELECTOR);
    if (s1) {
      e.preventDefault();
      handleStep1Select(s1);
      return;
    }

    const s2 = e.target.closest(STEP2_SELECTOR);
    if (s2) {
      e.preventDefault();
      handleStep2Select(s2);
      return;
    }

    const nextBtn = e.target.closest('#az-next-btn');
    if (nextBtn) {
      e.preventDefault();
      handleNextClick();
      return;
    }

    const backBtn = e.target.closest('#az-back-btn');
    if (backBtn) {
      e.preventDefault();
      handleBackClick();
      return;
    }
  });

  document.addEventListener('keydown', function(e) {
    if (e.key !== 'Enter' && e.key !== ' ') return;
    const s1 = e.target.closest(STEP1_SELECTOR);
    if (s1) {
      e.preventDefault();
      handleStep1Select(s1);
    }
    const s2 = e.target.closest(STEP2_SELECTOR);
    if (s2) {
      e.preventDefault();
      handleStep2Select(s2);
    }
  });

  /* ==== init ==== */
  function init() {
    enhanceSelectable(STEP1_IDS.filter(id => $(id)));
    enhanceSelectable(STEP2_IDS.filter(id => $(id)));
    setStep(1);
  }
  if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init);
  else init();

  // Elementor hooks + Observer برای لود تنبل
  if (window.elementorFrontend && elementorFrontend.hooks) {
    const reEnhance = () => {
      enhanceSelectable(STEP1_IDS.filter(id => $(id)));
      enhanceSelectable(STEP2_IDS.filter(id => $(id)));
    };
    elementorFrontend.hooks.addAction('frontend/element_ready/global', reEnhance);
    elementorFrontend.hooks.addAction('popup:after_open', reEnhance);
  }
  const obs = new MutationObserver(() => {
    const anyNew = [...STEP1_IDS, ...STEP2_IDS].some(id => $(id));
    if (anyNew) {
      enhanceSelectable(STEP1_IDS.filter(id => $(id)));
      enhanceSelectable(STEP2_IDS.filter(id => $(id)));
    }
  });
  obs.observe(document.documentElement, { childList: true, subtree: true });
})();

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.send-video-data').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    const apCode = this.dataset.aparat;
                    const videoName = this.dataset.name;
                    const videoId   = this.dataset.id;

                    // ساخت مدال ریسپانسیو
                    const modal = document.createElement('div');
                    modal.style.cssText = `
        display:flex;
        position:fixed;
        top:0; left:0;
        width:100%; height:100%;
        background:rgba(0,0,0,0.7);
        z-index:9999;
        justify-content:center;
        align-items:center;
      `;

                    modal.innerHTML = `
        <div class="modal-box">
          <span class="closeModal">&times;</span>
          <div class="modal-content-inner">در حال بارگذاری...</div>
        </div>
        <style>
          .modal-box {
            color:white;
            font-family:'ایران‌سنسX';
            background:#transparent;
            border-radius:12px;
            width:90%;
            max-width:1194px; /* دسکتاپ */
            max-height:664px;
            overflow:auto;
            position:relative;
          }
          .modal-box .closeModal {
          display:none;
            position:absolute;
            top:0px;
            right:0px;
            cursor:pointer;
            font-size:28px;
            font-weight:bold;
            z-index:999999;
          }

          @media (max-width:768px) {
            .modal-box {
              width:95%;
              max-width:95%;
              padding:15px;
            }
          }
        </style>
      `;

                    document.body.appendChild(modal);
                    const url = `${window.location.origin}/wp-admin/admin-ajax.php?action=load_elementor_template&apcode=${encodeURIComponent(apCode)}&vname=${encodeURIComponent(videoName)}&vid=${encodeURIComponent(videoId)}`;
                    fetch(url)
                        .then(res => res.text())
                        .then(html => {
                            const container = modal.querySelector('.modal-content-inner');
                            container.innerHTML = html;

                        });
                    // بستن مدال
                    modal.querySelector('.closeModal').addEventListener('click', () => document.body.removeChild(modal));
                    modal.addEventListener('click', e => { if(e.target === modal) document.body.removeChild(modal); });
                });
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            // اکاردئون اصلی
            document.querySelectorAll('.accordion-header').forEach(header => {
                header.addEventListener('click', function() {
                    const targetId = this.dataset.target;
                    const content = document.getElementById(targetId);
                    const isActive = this.classList.contains('active');

                    // بستن همه
                    document.querySelectorAll('.accordion-header').forEach(h => h.classList.remove('active'));
                    document.querySelectorAll('.accordion-content').forEach(c => c.style.display = 'none');

                    // باز کردن فقط اگر قبلا باز نبود
                    if (!isActive) {
                        this.classList.add('active');
                        content.style.display = 'block';
                    }
                });
            });

            // اکاردئون درس‌ها
            document.querySelectorAll('.lesson-header').forEach(header => {
                header.addEventListener('click', function() {
                    const targetId = this.dataset.target;
                    const content = document.getElementById(targetId);
                    const isActive = this.classList.contains('active');

                    // بستن همه درس‌ها در همان آزمون
                    const parentAccordion = this.closest('.accordion-content');
                    parentAccordion.querySelectorAll('.lesson-header').forEach(h => h.classList.remove('active'));
                    parentAccordion.querySelectorAll('.lesson-content').forEach(c => c.style.display = 'none');

                    if (!isActive) {
                        this.classList.add('active');
                        content.style.display = 'block';
                    }
                });
            });
        });

