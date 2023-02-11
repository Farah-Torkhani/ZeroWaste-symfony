document.addEventListener("DOMContentLoaded", function () {
  var calendarEl = document.getElementById("calendar");

  var calendar = new FullCalendar.Calendar(calendarEl, {
    initialView: "dayGridMonth",
    headerToolbar: {
      start: "prev,next,today",
      center: "title",
      end: "dayGridMonth,timeGridWeek,timeGridDay,listWeek",
    },
    events: [
      {
        title: "camping",
        start: "2023-03-01",
        backgroundColor: "#FFE9E9",
        borderColor: "#FFE9E9",
        textColor: "#FF4D49",
        end: "2023-03-04",
      },
      {
        title: "camping",
        start: "2023-02-06T09:00:00",
        backgroundColor: "#FFE9E9",
        borderColor: "#FFE9E9",
        textColor: "#FF4D49",
        end: "2023-02-07T11:00:00",
      },
      {
        title: "Riding bikes",
        start: "2023-01-29",
        backgroundColor: "rgba(253,181,40,0.12)",
        borderColor: "rgba(253,181,40,0.12)",
        textColor: "rgba(253,181,40)",
      },
      {
        title: "camping",
        start: "2023-02-16",
        backgroundColor: "rgba(253,181,40,0.12)",
        borderColor: "rgba(253,181,40,0.12)",
        textColor: "rgba(253,181,40)",
        end: "2023-02-19",
      },
      {
        title: "planting",
        start: "2023-02-19",
        backgroundColor: "rgba(114,225,40, 0.12)",
        borderColor: "rgba(114,225,40, 0.12)",
        textColor: "rgba(114,225,40)",
      },
      {
        title: "planting",
        start: "2023-02-06",
        backgroundColor: "rgba(114,225,40, 0.12)",
        borderColor: "rgba(114,225,40, 0.12)",
        textColor: "rgba(114,225,40)",
      },
      {
        title: "Riding bikes",
        start: "2023-02-09",
        backgroundColor: "rgba(102,108,255, 0.12)",
        borderColor: "rgba(102,108,255, 0.12)",
        textColor: "rgba(102,108,255)",
      },

      {
        title: "camping",
        start: "2023-02-22",
        backgroundColor: "rgb(38,198,249,0.12)",
        borderColor: "rgb(38,198,249,0.12)",
        textColor: "rgb(38,198,249)",
        end: "2023-02-24",
      },
      {
        title: "Swimming",
        start: "2023-02-04",
        backgroundColor: "rgb(38,198,249,0.12)",
        borderColor: "rgb(38,198,249,0.12)",
        textColor: "rgb(38,198,249)",
      },
      {
        title: "Camping",
        start: "2023-02-13",
        backgroundColor: "rgb(38,198,249,0.12)",
        borderColor: "rgb(38,198,249,0.12)",
        textColor: "rgb(38,198,249)",
      },

      {
        title: "meeting",
        start: "2023-02-01T07:10:00",
        backgroundColor: "rgba(102,108,255, 0.12)",
        borderColor: "rgba(102,108,255, 0.12)",
        textColor: "rgba(102,108,255)",
        extendedProps: {
          status: "done",
        },
        url: "http://127.0.0.1:5500/index.html",
      },
      {
        title: "meeting",
        start: "2023-02-14T20:00:00",
        backgroundColor: "rgba(102,108,255, 0.12)",
        borderColor: "rgba(102,108,255, 0.12)",
        textColor: "rgba(102,108,255)",
        url: "http://127.0.0.1:5500/index.html",
      },
      {
        title: "meeting",
        start: "2023-02-11T10:00:00",
        backgroundColor: "rgba(102,108,255, 0.12)",
        borderColor: "rgba(102,108,255, 0.12)",
        textColor: "rgba(102,108,255)",
        url: "http://127.0.0.1:5500/index.html",
      },
    ],
    editable: true,
    navLinks: true,

    eventResizableFromStart: true,
    height: 600,

    dateClick: function (info) {
      alert("Date: " + info.dateStr);
      alert("Resource ID: " + info.resource.id);
      info.dayEl.style.backgroundColor = "red";
    },
  });

  calendar.on("eventChange", (e) => {
    console.log(e);
  });

  calendar.render();
});
