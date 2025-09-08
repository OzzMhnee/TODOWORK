import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static values = {
        csrfToken: String
    }
    connect() {

        const el = document.getElementById('checklists-sortable');
        if (el) {
            new Sortable(el, {
                animation: 150,
                handle: '.drag-handle-checklist',
                onEnd: (evt) => {
                    // Récupère l'ordre des IDs
                    const ids = Array.from(el.querySelectorAll('li[data-checklist-id]')).map(li => li.getAttribute('data-checklist-id'));
                    fetch('/checklist/reorder', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': this.csrfTokenValue
                        },
                        body: JSON.stringify({
                            card_id: el.getAttribute('data-card-id'),
                            checklist_ids: ids
                        })
                    }).then(r => r.json()).then(data => {
                        if (data.success) {
                        } else {
                            // Optionnel : afficher une erreur
                            alert(data.error || "Erreur lors du réordonnancement");
                        }
                    });
                }
            });
        }
    }
}
