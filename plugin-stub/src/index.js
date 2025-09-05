import React from 'react';
import { createRoot } from 'react-dom/client';
// import './styles/admin.scss'; // Include your custom Sass styles for admin panel

const AdminComponent = () => (
    <div className="admin-component">
        <h1>Admin Component</h1>
        {/* Your admin-specific code */}
    </div>
);

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('my-app');
    const root = createRoot(container);
    root.render(<AdminComponent />);
});
