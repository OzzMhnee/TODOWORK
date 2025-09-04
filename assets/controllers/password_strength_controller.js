import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['password'];

    connect() {
        this.passwordTarget.addEventListener('input', this.checkStrength.bind(this));
    }

    checkStrength() {
        const val = this.passwordTarget.value;
        document.getElementById('pw-length').checked = val.length >= 8;
        document.getElementById('pw-upper').checked = /[A-Z]/.test(val);
        document.getElementById('pw-lower').checked = /[a-z]/.test(val);
        document.getElementById('pw-digit').checked = /\d/.test(val);
        document.getElementById('pw-special').checked = /[\W_]/.test(val);
    }
}
