document.querySelectorAll('[data-avatar-color]').forEach((element) => {
    element.style.backgroundColor = element.dataset.avatarColor;
});

document.querySelectorAll('[data-activity-color]').forEach((element) => {
    element.style.setProperty('--activity-color', element.dataset.activityColor);
});
