/**
 * Real-time Fleet Telemetry Engine
 * Polls live fleet telemetry, updates status cards, drives live sync beacon,
 * and feeds real-time notifications to the shadcn UI.
 */

(function () {
    'use strict';

    const FleetRealtime = {
        pollIntervalMs: 12000,
        timerId: null,
        lastSyncTimestamp: Date.now(),
        secondsAgoTimer: null,
        isSyncing: false,

        transitEvents: [
            { icon: 'bus', title: 'GPS Ping Received', msg: 'Bus #102 reporting on Metro Expressway (62 km/h)' },
            { icon: 'activity', title: 'Telemetry Uplink', msg: 'Depot Gateway 01 synchronized 18 mobile telemetry units' },
            { icon: 'shield-check', title: 'Route Geofence Cleared', msg: 'Bus #104 checked into Terminal 4 on schedule' },
            { icon: 'gauge', title: 'Odometer Auto-Audit', msg: 'Digital meter stream validated via optical AI sync' },
            { icon: 'fuel', title: 'Fuel Economy Ping', msg: 'Fleet average calculated: 4.2 km/L across active shifts' },
        ],

        init() {
            this.updateSyncTimer();
            this.startPolling();
            this.bindSyncButton();
            this.startSimulatedEvents();
        },

        bindSyncButton() {
            const syncBtn = document.getElementById('telemetrySyncBtn');
            if (!syncBtn) return;

            syncBtn.addEventListener('click', () => {
                this.syncNow(true);
            });
        },

        startPolling() {
            this.timerId = setInterval(() => {
                this.syncNow(false);
            }, this.pollIntervalMs);

            // Update "Synced Xs ago" text every second
            this.secondsAgoTimer = setInterval(() => {
                this.updateSyncTimer();
            }, 1000);
        },

        updateSyncTimer() {
            const label = document.getElementById('telemetryLastSync');
            if (!label) return;

            const diffSec = Math.floor((Date.now() - this.lastSyncTimestamp) / 1000);
            if (diffSec < 2) {
                label.textContent = 'Synced just now';
            } else {
                label.textContent = `Synced ${diffSec}s ago`;
            }
        },

        async syncNow(showToast = false) {
            if (this.isSyncing) return;
            this.isSyncing = true;

            const syncIcon = document.getElementById('telemetrySyncIcon');
            if (syncIcon) {
                syncIcon.classList.add('animate-spin');
            }

            try {
                const response = await fetch('/telemetry/live', {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    this.lastSyncTimestamp = Date.now();
                    this.updateSyncTimer();

                    if (data && data.stats) {
                        this.updateDOMStats(data.stats);
                    }

                    if (data && data.unread_notifications_count !== undefined) {
                        this.updateNotifications(data.unread_notifications_count, data.recent_notifications || []);
                    }

                    if (showToast && window.toast) {
                        window.toast.success(
                            'Telemetry Synced',
                            `Active: ${data.stats.activeBuses} vehicles | Total: ${data.stats.totalBuses} | ${data.formatted_time}`
                        );
                    }
                }
            } catch (err) {
                console.warn('Telemetry poll deferred:', err.message);
            } finally {
                this.isSyncing = false;
                if (syncIcon) {
                    setTimeout(() => syncIcon.classList.remove('animate-spin'), 400);
                }
            }
        },

        updateNotifications(unreadCount, recentNotifs) {
            const ping = document.getElementById('topbarNotificationPing');
            const badge = document.getElementById('topbarUnreadBadge');
            
            if (ping) {
                if (unreadCount > 0) {
                    ping.classList.remove('hidden');
                } else {
                    ping.classList.add('hidden');
                }
            }

            if (badge) {
                badge.textContent = `${unreadCount} unread`;
                badge.className = unreadCount > 0
                    ? 'text-[10px] font-mono px-1.5 py-0.2 rounded-full bg-primary/10 text-primary font-bold'
                    : 'text-[10px] font-mono px-1.5 py-0.2 rounded-full bg-muted text-muted-foreground';
            }

            // Check if any fresh unread notification should trigger a toast
            if (Array.isArray(recentNotifs) && window.toast) {
                if (!this.seenNotificationIds) {
                    this.seenNotificationIds = new Set(recentNotifs.map(n => n.id));
                } else {
                    recentNotifs.forEach(n => {
                        if (!n.is_read && !this.seenNotificationIds.has(n.id)) {
                            this.seenNotificationIds.add(n.id);
                            window.toast.info(n.title, n.message);
                        }
                    });
                }
            }
        },

        updateDOMStats(stats) {
            // Update any elements tagged with data-telemetry-key
            Object.keys(stats).forEach(key => {
                const els = document.querySelectorAll(`[data-telemetry-key="${key}"]`);
                els.forEach(el => {
                    const oldVal = el.textContent.trim();
                    const newVal = typeof stats[key] === 'number' && key.includes('Cost')
                        ? '$' + Number(stats[key]).toLocaleString()
                        : typeof stats[key] === 'number' && key.includes('Odometer')
                            ? Number(stats[key]).toLocaleString()
                            : String(stats[key]);

                    if (oldVal !== newVal) {
                        el.textContent = newVal;
                        el.classList.add('text-emerald-500', 'transition-colors');
                        setTimeout(() => {
                            el.classList.remove('text-emerald-500');
                        }, 1200);
                    }
                });
            });

            // Update utilization badge if present
            const utilEl = document.querySelector('[data-telemetry-key="utilizationPct"]');
            if (utilEl && stats.utilizationPct !== undefined) {
                utilEl.textContent = stats.utilizationPct + '% Cap';
            }
        },

        startSimulatedEvents() {
            // Occasionally fire a realistic fleet telemetry toast every 45-60 seconds
            setInterval(() => {
                const event = this.transitEvents[Math.floor(Math.random() * this.transitEvents.length)];
                if (window.toast && Math.random() > 0.4) {
                    window.toast.telemetry(event.title, event.msg);
                }
            }, 45000);
        }
    };

    window.fleetRealtime = FleetRealtime;

    document.addEventListener('DOMContentLoaded', () => {
        FleetRealtime.init();
    });
})();
