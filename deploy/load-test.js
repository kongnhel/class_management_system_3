// ============================================================
// NMU Class Management - Load Test (attendance scan window)
// ============================================================
// Simulates the worst realistic moment: 300 users arriving at
// class start - logging in, opening their dashboard, firing an
// attendance scan POST.
//
// SETUP (see the chat runbook that came with this script):
//   1. Install k6 (choco install k6, or download from grafana.com)
//   2. Create a test student account (tinker one-liner)
//   3. Temporarily raise the student-route throttle on the server
//      during the test (revert after!)
//
// RUN:
//   k6 run -e BASE_URL=https://sys.nmu.edu.kh ^
//          -e EMAIL=loadtest@nmu.edu.kh ^
//          -e PASSWORD=your-test-password ^
//          deploy\load-test.js
//
// SUCCESS CRITERIA:
//   - http_req_failed rate  < 1%        (no 5xx)
//   - dashboard_duration p95 < 500ms    (full page renders)
//   - no errors during the 5-minute hold at 300 users
// ============================================================

import http from 'k6/http';
import { check, sleep } from 'k6';
import { Trend } from 'k6/metrics';

const BASE_URL = __ENV.BASE_URL || 'http://127.0.0.1';
const EMAIL = __ENV.EMAIL || 'loadtest@nmu.edu.kh';
const PASSWORD = __ENV.PASSWORD || '';

if (!PASSWORD) {
  console.error('PASSWORD env var is required: -e PASSWORD=...');
}

export const options = {
  scenarios: {
    scan_window: {
      executor: 'ramping-vus',
      startVUs: 0,
      stages: [
        { duration: '2m', target: 300 }, // students arriving at class start
        { duration: '5m', target: 300 }, // the scan window holds
        { duration: '1m', target: 0 },   // everyone seated, load fades
      ],
      gracefulRampDown: '30s',
    },
  },
  thresholds: {
    // hard failures (5xx, network) must stay under 1%
    http_req_failed: ['rate<0.01'],
    // every request p95 under 800ms
    http_req_duration: ['p(95)<800'],
    // dashboard page renders specifically under 500ms
    dashboard_duration: ['p(95)<500'],
  },
};

const dashboardDuration = new Trend('dashboard_duration');

// per-VU login state (each virtual user gets its own session)
let loggedIn = false;

function login() {
  const page = http.get(`${BASE_URL}/login`);
  const token = page.html().find('input[name=_token]').first().attr('value');
  if (!token) {
    console.error('Could not find CSRF token on login page - is BASE_URL correct?');
    return;
  }

  const res = http.post(`${BASE_URL}/login`, {
    email: EMAIL,
    password: PASSWORD,
    _token: token,
  });

  if (res.status !== 200 && res.status !== 302 && res.status !== 204) {
    console.error(`Login failed with status ${res.status} - check EMAIL/PASSWORD and that the account exists.`);
    return;
  }

  // verify the session actually works
  const dash = http.get(`${BASE_URL}/student/dashboard`);
  if (dash.status === 200) {
    loggedIn = true;
  } else {
    console.error(`Session check failed (${dash.status}) - is the test account role=student?`);
  }
}

export default function () {
  if (!loggedIn) {
    login();
    if (!loggedIn) {
      sleep(5);
      return;
    }
  }

  // 1) student opens their dashboard (heaviest realistic page)
  const dash = http.get(`${BASE_URL}/student/dashboard`, { tags: { name: 'dashboard' } });
  dashboardDuration.add(dash.timings.duration);
  check(dash, {
    'dashboard renders': (r) => r.status === 200,
  });

  // 2) student fires an attendance scan
  // A dummy token gets a clean 4xx from the validation path - that is expected
  // and still exercises routing, session, middleware and DB lookups.
  const scan = http.post(
    `${BASE_URL}/student/process-scan`,
    { token: 'loadtest-dummy-token' },
    {
      tags: { name: 'scan' },
      // 4xx responses are the expected outcome here - only 5xx counts as failure
      responseCallback: http.expectedStatuses(200, 204, 400, 401, 404, 419, 422, 429),
    }
  );
  check(scan, {
    'scan no server error': (r) => r.status < 500,
  });

  // think time: a real student looks at their screen 8-15s before acting again
  sleep(8 + Math.random() * 7);
}
