import './bootstrap.js';
import './styles/app.css';
import { startStimulusApp } from '@symfony/stimulus-bundle';
import passwordStrengthController from './controllers/password_strength_controller.js';
import flashMessagesController from './controllers/flash_messages_controller.js';
import dragDropChecklistController from './controllers/drag_drop_checklist_controller.js';

const app = startStimulusApp();
app.register('password-strength', passwordStrengthController);
app.register('flash-messages', flashMessagesController);
app.register('drag_drop_checklist', dragDropChecklistController);

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');;
