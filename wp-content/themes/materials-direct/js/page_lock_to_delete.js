(function($) {

    // add releasePageLock(); to the .delivery-options-modal__submit success handler

    let lockInitialized = false;

    const ACTIVE_TAB_KEY = 'wc_active_tab';
    const tabId = Date.now() + '_' + Math.random().toString(36).substr(2, 9);

    function initPageLock() {

    if (lockInitialized) return;
    lockInitialized = true;

    console.log('LOCK INITIALISED', tabId);

    checkLock();

    // Listen for other tabs changing active tab
    window.addEventListener('storage', function (event) {
    if (event.key === ACTIVE_TAB_KEY) {
    checkLock();
    }
    });
    }

    function getActiveTab() {
    return localStorage.getItem(ACTIVE_TAB_KEY);
    }

    function setActiveTab() {
    localStorage.setItem(ACTIVE_TAB_KEY, tabId);
    }

    function releaseLock() {
    const activeTab = getActiveTab();

    if (activeTab === tabId) {
    localStorage.removeItem(ACTIVE_TAB_KEY);
    console.log('LOCK RELEASED');
    }
    }

    window.releasePageLock = releaseLock;

    function showWarning() {

        if (document.getElementById('wc-multi-tab-warning')) return;

        const div = document.createElement('div');
        div.id = 'wc-multi-tab-warning';

        Object.assign(div.style, {
            background: '#cc0000',
            color: '#fff',
            padding: '10px 15px',
            marginBottom: '15px',
            textAlign: 'center',
            fontSize: '14px',
            lineHeight: '17px',
            borderRadius: '4px'
        });

        div.innerText = 'Customer message: Multiple windows or tabs are not permitted for this kind of order due to session conflicts. Please close your second browser window to continue';

        const container = document.querySelector('.delivery-options-modal__content');

        if (container) {
            container.prepend(div); // insert at top of modal content
        }
    }

    /*
    function disableModal() {

    $('.delivery-options-modal input, .delivery-options-modal select, .delivery-options-modal button')
    .prop('disabled', true)
    .css('opacity', '0.5');

    }
    */

    function checkLock() {
        const activeTab = getActiveTab();
        
        // No active tab → claim ownership
        if (!activeTab) {
            setActiveTab();
            return;
        }

        // This tab owns it → all good
        if (activeTab === tabId) {
            return;
        }

        // === NEW: Detect stale lock ===
        const activeTimestampStr = activeTab.split('_')[0];
        const activeTimestamp = parseInt(activeTimestampStr, 10);
        const now = Date.now();
        const MAX_LOCK_AGE_MS = 30 * 60 * 1000; // 30 minutes (adjust if you want)

        if (isNaN(activeTimestamp) || (now - activeTimestamp > MAX_LOCK_AGE_MS)) {
            console.log('STALE LOCK DETECTED from', activeTab, '- claiming for this tab');
            setActiveTab();
            return;
        }

        // Real conflict
        console.log('ANOTHER TAB ACTIVE:', activeTab);
        showWarning();
        //disableModal();
    }

    /*
    function checkLock() {

    const activeTab = getActiveTab();

    // No active tab → claim ownership
    if (!activeTab) {
    setActiveTab();
    return;
    }

    // This tab owns it → all good
    if (activeTab === tabId) {
    return;
    }

    // Another tab is actively using modal
    console.log('ANOTHER TAB ACTIVE:', activeTab);

    showWarning();
    disableModal();
    }
    */

    // ============================
    // EVENT HOOKS
    // ============================

    // Modal opened → attempt to claim lock
    $(document).on('click', '#add_shipments', function() {
    initPageLock();
    });

    // Modal closed → release lock
    $(document).on('click', '.delivery-options-modal__close-btn', function() {
    releaseLock();
    lockInitialized = false;
    });

    // OPTIONAL: also release lock when page unloads
    window.addEventListener('beforeunload', function () {
    releaseLock();
    });

})(jQuery);
