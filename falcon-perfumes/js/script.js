
   FALCON PERFUMES — script.js
   Features implemented:
   1. Dynamic Content Updates — brand/category filtering
   2. Interactive Image Slider — hero carousel (manual + auto)
   3. Form Validation — login & contact, real-time feedback
   4. Smooth Scrolling — nav anchor links
   5. Event Handling — add-to-cart, hover/tab, toasts
   6. Custom Animations — scroll reveal, cart bump, fades

document.addEventListener("DOMContentLoaded", () => {

 
  const CART_KEY = "falcon_cart_count";
  const getCartCount = () => parseInt(localStorage.getItem(CART_KEY) || "0", 10);
  const setCartCount = (n) => localStorage.setItem(CART_KEY, String(n));

  function refreshCartBadge(){
    document.querySelectorAll("[data-cart-count]").forEach(el => {
      el.textContent = getCartCount();
    });
  }
  refreshCartBadge();

  function bumpCartBadge(){
    document.querySelectorAll("[data-cart-count]").forEach(el => {
      el.classList.remove("bump");
      void el.offsetWidth; 
      el.classList.add("bump");
    });
  }


  let toastTimer;
  function showToast(message){
    let toast = document.querySelector(".toast-fp");
    if(!toast){
      toast = document.createElement("div");
      toast.className = "toast-fp";
      toast.innerHTML = `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg><span class="toast-msg"></span>`;
      document.body.appendChild(toast);
    }
    toast.querySelector(".toast-msg").textContent = message;
    toast.classList.add("show");
    clearTimeout(toastTimer);
    toastTimer = setTimeout(() => toast.classList.remove("show"), 2600);
  }

  
  document.querySelectorAll(".add-cart-btn").forEach(btn => {
    btn.addEventListener("click", () => {
      const name = btn.closest(".product-card, .product-body")?.querySelector(".product-name")?.textContent?.trim() || "Item";
      setCartCount(getCartCount() + 1);
      refreshCartBadge();
      bumpCartBadge();

      const original = btn.textContent;
      btn.textContent = "Added ✓";
      btn.classList.add("added");
      btn.disabled = true;
      showToast(`${name} added to cart`);

      setTimeout(() => {
        btn.textContent = original;
        btn.classList.remove("added");
        btn.disabled = false;
      }, 1400);
    });
  });


  const slider = document.querySelector("[data-hero-slider]");
  if(slider){
    const slides = [...slider.querySelectorAll(".hero-slide")];
    const dotsWrap = slider.querySelector("[data-hero-dots]");
    const prevBtn = slider.querySelector("[data-hero-prev]");
    const nextBtn = slider.querySelector("[data-hero-next]");
    let current = 0;
    let autoTimer;

    slides.forEach((_, i) => {
      const dot = document.createElement("button");
      dot.className = "hero-dot" + (i === 0 ? " active" : "");
      dot.setAttribute("aria-label", `Go to slide ${i + 1}`);
      dot.addEventListener("click", () => goTo(i));
      dotsWrap.appendChild(dot);
    });
    const dots = [...dotsWrap.querySelectorAll(".hero-dot")];

    function goTo(index){
      slides[current].classList.remove("active");
      dots[current].classList.remove("active");
      current = (index + slides.length) % slides.length;
      slides[current].classList.add("active");
      dots[current].classList.add("active");
      restartAuto();
    }
    function next(){ goTo(current + 1); }
    function prev(){ goTo(current - 1); }
    function restartAuto(){
      clearInterval(autoTimer);
      autoTimer = setInterval(next, 5500);
    }
    nextBtn?.addEventListener("click", next);
    prevBtn?.addEventListener("click", prev);
    restartAuto();
  }

  
  const filterBar = document.querySelector("[data-filter-bar]");
  if(filterBar){
    const pills = [...filterBar.querySelectorAll(".filter-pill")];
    const items = [...document.querySelectorAll("[data-category]")];
    pills.forEach(pill => {
      pill.addEventListener("click", () => {
        pills.forEach(p => p.classList.remove("active"));
        pill.classList.add("active");
        const target = pill.dataset.filter;
        items.forEach(item => {
          const show = target === "all" || item.dataset.category === target;
          item.classList.toggle("hide", !show);
        });
      });
    });
  }

 
  document.querySelectorAll('a[href^="#"]').forEach(link => {
    link.addEventListener("click", (e) => {
      const id = link.getAttribute("href");
      if(id.length > 1){
        const target = document.querySelector(id);
        if(target){
          e.preventDefault();
          target.scrollIntoView({ behavior: "smooth", block: "start" });
        }
      }
    });
  });


  const revealEls = document.querySelectorAll(".reveal");
  if("IntersectionObserver" in window && revealEls.length){
    const io = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if(entry.isIntersecting){
          entry.target.classList.add("in");
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.15 });
    revealEls.forEach(el => io.observe(el));
  } else {
    revealEls.forEach(el => el.classList.add("in"));
  }

  
  function setFieldState(input, hintEl, valid, message){
    input.classList.toggle("is-invalid", !valid);
    input.classList.toggle("is-valid", valid);
    if(hintEl){
      hintEl.textContent = message || "";
      hintEl.classList.toggle("err", !valid);
      hintEl.classList.toggle("ok", valid && !!message);
    }
  }

  const emailRe = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  function validateEmailField(input){
    const hint = input.closest(".fp-field")?.querySelector(".field-hint");
    if(input.value.trim() === ""){ setFieldState(input, hint, false, "Email address is required"); return false; }
    if(!emailRe.test(input.value.trim())){ setFieldState(input, hint, false, "Enter a valid email address"); return false; }
    setFieldState(input, hint, true, "Looks good");
    return true;
  }
  function validateRequiredField(input, label){
    const hint = input.closest(".fp-field")?.querySelector(".field-hint");
    if(input.value.trim() === ""){ setFieldState(input, hint, false, `${label} is required`); return false; }
    setFieldState(input, hint, true, "");
    return true;
  }
  function validatePasswordField(input, min = 6){
    const hint = input.closest(".fp-field")?.querySelector(".field-hint");
    if(input.value.length === 0){ setFieldState(input, hint, false, "Password is required"); return false; }
    if(input.value.length < min){ setFieldState(input, hint, false, `Use at least ${min} characters`); return false; }
    setFieldState(input, hint, true, "Strong enough"); return true;
  }

  
  const loginForm = document.querySelector("#loginForm");
  if(loginForm){
    const email = loginForm.querySelector("#loginEmail");
    const pass = loginForm.querySelector("#loginPassword");
    email?.addEventListener("input", () => validateEmailField(email));
    pass?.addEventListener("input", () => validatePasswordField(pass));
    loginForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const okE = validateEmailField(email);
      const okP = validatePasswordField(pass);
      if(okE && okP){ showToast("Welcome back — login successful"); loginForm.reset();
        [email, pass].forEach(i => i.classList.remove("is-valid","is-invalid")); }
    });
  }

  
  const signupForm = document.querySelector("#signupForm");
  if(signupForm){
    const name = signupForm.querySelector("#signupName");
    const email = signupForm.querySelector("#signupEmail");
    const pass = signupForm.querySelector("#signupPassword");
    name?.addEventListener("input", () => validateRequiredField(name, "Full name"));
    email?.addEventListener("input", () => validateEmailField(email));
    pass?.addEventListener("input", () => validatePasswordField(pass));
    signupForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const okN = validateRequiredField(name, "Full name");
      const okE = validateEmailField(email);
      const okP = validatePasswordField(pass);
      if(okN && okE && okP){ showToast("Account created — welcome to Falcon Perfumes"); signupForm.reset();
        [name, email, pass].forEach(i => i.classList.remove("is-valid","is-invalid")); }
    });
  }

  
  const contactForm = document.querySelector("#contactForm");
  if(contactForm){
    const cname = contactForm.querySelector("#contactName");
    const cemail = contactForm.querySelector("#contactEmail");
    const cmsg = contactForm.querySelector("#contactMessage");
    cname?.addEventListener("input", () => validateRequiredField(cname, "Your name"));
    cemail?.addEventListener("input", () => validateEmailField(cemail));
    cmsg?.addEventListener("input", () => validateRequiredField(cmsg, "Message"));
    contactForm.addEventListener("submit", (e) => {
      e.preventDefault();
      const okN = validateRequiredField(cname, "Your name");
      const okE = validateEmailField(cemail);
      const okM = validateRequiredField(cmsg, "Message");
      if(okN && okE && okM){
        showToast("Message sent — we'll reply within 24 hours");
        contactForm.reset();
        [cname, cemail, cmsg].forEach(i => i.classList.remove("is-valid","is-invalid"));
      }
    });
  }

  
  document.querySelectorAll(".fp-toggle-pass").forEach(btn => {
    btn.addEventListener("click", () => {
      const input = btn.previousElementSibling;
      const isPass = input.type === "password";
      input.type = isPass ? "text" : "password";
      btn.innerHTML = isPass
        ? `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.94 17.94A10.94 10.94 0 0112 20C5 20 1 12 1 12a19.9 19.9 0 015.06-6.06M9.9 4.24A10.94 10.94 0 0112 4c7 0 11 8 11 8a19.9 19.9 0 01-3.22 4.44M14.12 14.12a3 3 0 11-4.24-4.24"/><path d="M1 1l22 22"/></svg>`
        : `<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>`;
    });
  });

  
  const authTabs = document.querySelectorAll("[data-auth-tab]");
  if(authTabs.length){
    authTabs.forEach(tab => {
      tab.addEventListener("click", () => {
        authTabs.forEach(t => t.classList.remove("active"));
        tab.classList.add("active");
        document.querySelectorAll("[data-auth-panel]").forEach(panel => {
          panel.classList.toggle("d-none", panel.dataset.authPanel !== tab.dataset.authTab);
        });
      });
    });
  }

 
  const nav = document.querySelector(".fp-navbar");
  if(nav){
    window.addEventListener("scroll", () => {
      nav.style.boxShadow = window.scrollY > 30 ? "0 10px 30px -18px rgba(0,0,0,.6)" : "none";
    });
  }

 
  document.querySelectorAll("[data-year]").forEach(el => el.textContent = new Date().getFullYear());
});
