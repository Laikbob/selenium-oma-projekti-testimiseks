document.addEventListener("DOMContentLoaded", function () {
    const photos = document.querySelectorAll(".car-photo");

    photos.forEach(photo => {
        photo.addEventListener("click", () => {
            photo.classList.toggle("expanded");
        });
    });
});
