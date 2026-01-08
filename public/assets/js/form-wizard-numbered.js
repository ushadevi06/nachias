$(function () {
  var e = $(".select2"),
    t = $(".selectpicker");
  t.length && (t.selectpicker(), handleBootstrapSelectEvents()),
    e.length &&
    e.each(function () {
      var e = $(this);
      select2Focus(e),
        e.select2({
          placeholder: "Select value",
          dropdownParent: e.parent(),
        });
    });
}),
  (() => {
    var e = document.querySelector(".wizard-numbered");
    if (null !== e) {
      var l = [].slice.call(e.querySelectorAll(".btn-next")),
        r = [].slice.call(e.querySelectorAll(".btn-prev")),
        c = e.querySelector(".btn-submit");
      let t = new Stepper(e, { linear: !1 });
      l &&
        l.forEach((e) => {
          e.addEventListener("click", (e) => {
            t.next();
          });
        }),
        r &&
        r.forEach((e) => {
          e.addEventListener("click", (e) => {
            t.previous();
          });
        }),
        c &&
        c.addEventListener("click", (e) => {
          alert("Submitted..!!");
        });
    }

    e = document.querySelector(".wizard-vertical");
    if (null !== e) {
      var l = [].slice.call(e.querySelectorAll(".btn-next")),
        r = [].slice.call(e.querySelectorAll(".btn-prev")),
        c = e.querySelector(".btn-submit");
      let t = new Stepper(e, { linear: !1 });
      l &&
        l.forEach((e) => {
          e.addEventListener("click", (e) => {
            t.next();
          });
        }),
        r &&
        r.forEach((e) => {
          e.addEventListener("click", (e) => {
            t.previous();
          });
        }),
        c &&
        c.addEventListener("click", (e) => {
          alert("Submitted..!!");
        });
    }

    e = document.querySelector(".wizard-modern-example");
    if (null !== e) {
      var l = [].slice.call(e.querySelectorAll(".btn-next")),
        r = [].slice.call(e.querySelectorAll(".btn-prev")),
        c = e.querySelector(".btn-submit");
      let t = new Stepper(e, { linear: !1 });
      l &&
        l.forEach((e) => {
          e.addEventListener("click", (e) => {
            t.next();
          });
        }),
        r &&
        r.forEach((e) => {
          e.addEventListener("click", (e) => {
            t.previous();
          });
        }),
        c &&
        c.addEventListener("click", (e) => {
          alert("Submitted..!!");
        });
    }

    e = document.querySelector(".wizard-modern-vertical");
    if (null !== e) {
      var l = [].slice.call(e.querySelectorAll(".btn-next")),
        r = [].slice.call(e.querySelectorAll(".btn-prev")),
        c = e.querySelector(".btn-submit");
      let t = new Stepper(e, { linear: !1 });
      l &&
        l.forEach((e) => {
          e.addEventListener("click", (e) => {
            t.next();
          });
        }),
        r &&
        r.forEach((e) => {
          e.addEventListener("click", (e) => {
            t.previous();
          });
        }),
        c &&
        c.addEventListener("click", (e) => {
          alert("Submitted..!!");
        });
    }
  })();
