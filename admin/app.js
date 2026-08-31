/**
 * RBSS Connect — Immersive Video Calling Bootloader
 * Connect. Collaborate. Communicate.
 */

window.MeetingState = {
  roomName: 'Product Strategy Meeting',
  roomId: 'RBSS-8492',
  localUser: 'Subash Sitaula',
  isMuted: false,
  isCamOff: false,
  isScreenSharing: false,
  isHandRaised: false,
  activeLayout: 'grid', // 'grid', 'speaker', 'sidebar'
  participants: [
    { id: 'host-subash', name: 'Subash Sitaula', role: 'Host', isMuted: false, isCamOff: false, isSpeaking: false, connection: 'excellent' },
    { id: 'peer-john', name: 'John Smith', role: 'Participant', isMuted: false, isCamOff: false, isSpeaking: true, connection: 'excellent' },
    { id: 'peer-sarah', name: 'Sarah Williams', role: 'Participant', isMuted: true, isCamOff: false, isSpeaking: false, connection: 'good' },
    { id: 'peer-michael', name: 'Michael Brown', role: 'Participant', isMuted: false, isCamOff: true, isSpeaking: false, connection: 'poor' },
    { id: 'peer-david', name: 'David Wilson', role: 'Participant', isMuted: true, isCamOff: true, isSpeaking: false, connection: 'reconnecting' },
    { id: 'peer-emily', name: 'Emily Taylor', role: 'Participant', isMuted: false, isCamOff: false, isSpeaking: false, connection: 'excellent' },
    { id: 'peer-james', name: 'James Carter', role: 'Participant', isMuted: false, isCamOff: false, isSpeaking: false, connection: 'excellent' },
    { id: 'peer-sofia', name: 'Sofia Rodriguez', role: 'Participant', isMuted: false, isCamOff: false, isSpeaking: false, connection: 'excellent' }
  ],
  chatMessages: [
    { sender: 'Sarah Williams', text: 'Can everyone see the presentation?', time: '14:20', isSelf: false },
    { sender: 'You', text: 'Yes, everything looks great.', time: '14:21', isSelf: true },
    { sender: 'John Smith', text: 'I will send the updated file.', time: '14:21', isSelf: false }
  ]
};

document.addEventListener('DOMContentLoaded', () => {
  // Setup timers
  initTimer();

  // Setup hotkeys
  window.addEventListener('keydown', handleGlobalKeydown);
});

function initTimer() {
  const timerEl = document.getElementById('meeting-timer');
  if (!timerEl) return;
  let elapsedSeconds = 2538; // 42 minutes 18 seconds baseline
  setInterval(() => {
    elapsedSeconds++;
    const m = Math.floor(elapsedSeconds / 60);
    const s = elapsedSeconds % 60;
    timerEl.textContent = `${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`;
  }, 1000);
}

function handleGlobalKeydown(e) { }