import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        setTimeout(() => {
            this.element.querySelectorAll('.flash-message').forEach((el) => {
                el.style.transition = 'opacity 0.5s';
                el.style.opacity = 0;
                setTimeout(() => el.remove(), 1500);
            });
        }, 3000);
    }
}
