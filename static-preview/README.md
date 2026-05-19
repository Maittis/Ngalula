# Ngalula Static Preview

This folder contains a self-contained static preview of the Ngalula unified app.

## Files
- `index.html` — static wrapper page that loads React, ReactDOM, Recharts, and the app script from CDN.
- `ngalula-unified.jsx` — client/admin app logic and UI.

## Deploying

### Netlify
1. Drag and drop this `static-preview` folder into Netlify Drop.
2. Or use the Netlify CLI from the repository root:
   ```bash
   netlify deploy --dir=static-preview --prod
   ```

### Vercel
1. Create a new Vercel project.
2. Select this `static-preview` folder as the project root.
3. Deploy directly; Vercel will serve `index.html` automatically.

## Notes
- This preview app uses the in-browser Babel transformer for `.jsx`.
- It is intended as a quick static demo and not a production build.
- If you want a production deployment, precompile the JSX and remove the Babel transform step.
