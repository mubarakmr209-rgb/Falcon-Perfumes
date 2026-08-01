
document.addEventListener("DOMContentLoaded", () => {
  const mount = document.querySelector("#site-footer");
  if(!mount) return;
  mount.innerHTML = `
    <div class="container">
      <div class="row g-5">
        <div class="col-lg-4">
          <a href="index.html" class="fp-brand d-inline-flex mb-3">
            <svg class="fp-wingmark" viewBox="0 0 48 48" fill="none">
              <path d="M24 4C18 14 6 18 4 20c6 0 12 2 16 8 4-6 10-8 16-8-2-2-14-6-16-16Z" stroke="#c9a24b" stroke-width="1.5" fill="rgba(201,162,75,.12)"/>
              <path d="M24 20v24" stroke="#c9a24b" stroke-width="1.5"/>
            </svg>
            <span>FALCON<small>PERFUMES</small></span>
          </a>
          <p>Premium, authentic perfumes for men, women, and unisex — delivered wherever you are.</p>
          <div class="social-row">
            <a href="#" class="social-fp" aria-label="Facebook"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M13.5 21v-7.5H16l.5-3H13.5V8.5c0-.9.25-1.5 1.55-1.5H16.5V4.3c-.28-.04-1.2-.12-2.28-.12-2.26 0-3.8 1.38-3.8 3.92V10.5H8v3h2.42V21z"/></svg></a>
            <a href="#" class="social-fp" aria-label="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/></svg></a>
            <a href="#" class="social-fp" aria-label="WhatsApp"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2a10 10 0 00-8.6 15L2 22l5.2-1.4A10 10 0 1012 2zm5.6 14.2c-.24.7-1.4 1.3-1.9 1.4-.5.1-1.1.15-3.5-.75-3-1.15-4.9-4.15-5-4.35-.15-.2-1.2-1.6-1.2-3s.75-2.15 1-2.4c.25-.25.55-.35.75-.35h.5c.15 0 .35 0 .55.4.24.5.75 1.75.8 1.9.05.15.1.3 0 .5-.1.2-.15.3-.3.45l-.4.5c-.15.15-.3.3-.15.6.15.3.7 1.2 1.5 1.9 1.05.95 1.9 1.25 2.2 1.4.3.15.5.1.65-.1.2-.2.7-.8.9-1.1.2-.3.4-.25.65-.15l1.7.8c.2.1.4.15.4.35.05.2.05.9-.2 1.6z"/></svg></a>
            <a href="#" class="social-fp" aria-label="TikTok"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M15 3c.3 1.9 1.6 3.4 3.6 3.7v2.6c-1.3 0-2.5-.4-3.6-1.1v6.5a5.3 5.3 0 11-4.6-5.3v2.7a2.6 2.6 0 102 2.5V3z"/></svg></a>
          </div>
        </div>
        <div class="col-lg-2 col-6">
          <h5>Shop</h5>
          <ul>
            <li><a href="new-arrivals.html">New Arrivals</a></li>
            <li><a href="brands.html">Brands</a></li>
            <li><a href="new-arrivals.html">Men</a></li>
            <li><a href="new-arrivals.html">Women</a></li>
          </ul>
        </div>
        <div class="col-lg-2 col-6">
          <h5>Company</h5>
          <ul>
            <li><a href="index.html">About Us</a></li>
            <li><a href="contact.html">Contact Us</a></li>
            <li><a href="login.html">My Account</a></li>
          </ul>
        </div>
        <div class="col-lg-4">
          <h5>Get in Touch</h5>
          <p>No. 123, Main Street, Colombo, Sri Lanka</p>
          <p>+94 77 123 4567 &nbsp;·&nbsp; info@falconperfumes.com</p>
        </div>
      </div>
      <div class="footer-bottom d-flex flex-wrap justify-content-between gap-2">
        <span>© <span data-year></span> Falcon Perfumes. All rights reserved.</span>
        <span>ICT1209 — Web Technologies · Mini Project</span>
      </div>
    </div>
  `;
 
  const y = mount.querySelector("[data-year]");
  if(y) y.textContent = new Date().getFullYear();
});
