document.addEventListener("DOMContentLoaded", function () {
  const html = document.documentElement;

  const themeButtons = document.querySelectorAll(".themeToggle");
  const rtlButtons = document.querySelectorAll(".rtlToggle");

  /* Load saved settings */
  const savedTheme = localStorage.getItem("theme");
  const savedDir = localStorage.getItem("direction");

  if (savedTheme === "dark") {
    html.setAttribute("data-theme", "dark");
    themeButtons.forEach(btn => btn.textContent = "☀️");
  }

  if (savedDir === "rtl") {
    html.setAttribute("dir", "rtl");
  }

  /* Theme toggle */
  themeButtons.forEach(btn => {
    btn.addEventListener("click", () => {
      const isDark = html.getAttribute("data-theme") === "dark";

      if (isDark) {
        html.removeAttribute("data-theme");
        localStorage.setItem("theme", "light");
        themeButtons.forEach(b => b.textContent = "🌙");
      } else {
        html.setAttribute("data-theme", "dark");
        localStorage.setItem("theme", "dark");
        themeButtons.forEach(b => b.textContent = "☀️");
      }
    });
  });

  /* RTL toggle */
  rtlButtons.forEach(btn => {
    btn.addEventListener("click", () => {
      const isRTL = html.getAttribute("dir") === "rtl";

      if (isRTL) {
        html.setAttribute("dir", "ltr");
        localStorage.setItem("direction", "ltr");
      } else {
        html.setAttribute("dir", "rtl");
        localStorage.setItem("direction", "rtl");
      }
    });
  });
});
