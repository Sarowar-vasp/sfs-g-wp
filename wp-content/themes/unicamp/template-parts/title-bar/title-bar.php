<div class="page-title-bar-inner"
     style="position: relative; height: 401px; display: flex; justify-content: flex-end; align-items: flex-end; padding: 0 30px 20px; box-sizing: border-box; overflow: hidden;">

  <div class="page-title-bar-bg" style="position: absolute; inset: 0;"></div>

  <!-- Independent floating bubbles layer -->
  <div style="position: absolute; inset: 0; overflow: hidden; z-index: 1; pointer-events: none;">
    <span style="position: absolute; bottom: 60px; right: 120px; width: 60px; height: 60px; border-radius: 50%;
                 background: rgba(157,34,53,0.15); animation: bubbleFloat1 10s ease-in-out infinite alternate;"></span>
    <span style="position: absolute; bottom: 20px; right: 80px; width: 40px; height: 40px; border-radius: 50%;
                 background: rgba(157,34,53,0.1); animation: bubbleFloat2 12s ease-in-out infinite alternate;"></span>
    <span style="position: absolute; bottom: 120px; right: 40px; width: 30px; height: 30px; border-radius: 50%;
                 background: rgba(157,34,53,0.2); animation: bubbleFloat3 14s ease-in-out infinite alternate;"></span>
    <span style="position: absolute; bottom: 40px; right: -20px; width: 70px; height: 70px; border-radius: 50%;
                 background: rgba(157,34,53,0.12); animation: bubbleFloat4 16s ease-in-out infinite alternate;"></span>
  </div>

  <!-- Breadcrumb bottom-left -->
  <div style="position: absolute; right: 20px; z-index: 3; text-align: right; display: inline-block; overflow: visible;">
    <?php unicamp_load_template( 'breadcrumb' ); ?>
  </div>

  <!-- Title bottom-right -->
  <!--display: inline-block;-->
  <div class="page-title-bar-heading container"
       style="display: none; position: absolute; bottom: 20px; right: 8px; z-index: 3; text-align: right; overflow: visible;">

    <!-- Typing text -->
    <div style="position: relative; z-index: 3; display: inline-block; background: transparent;">
      <span style="padding:4px 8px; display:inline-block; overflow:hidden; white-space:nowrap;
      border-right:.18em solid rgba(255,255,255,0.9);
      --chars:90; max-width:0ch;
      animation:typingLoop 14s steps(var(--chars), end) infinite, caretBlink 2s step-end infinite;
      background:linear-gradient(90deg, rgba(154,0,0,0.8) 0%, rgba(157,34,53,0.5) 60%, rgba(255,255,255,0) 100%);
      color:#fff; border-radius:6px;">
      <?php Unicamp_THA::instance()->title_bar_heading_before(); ?>
      <?php Unicamp_Title_Bar::instance()->render_title(); ?>
      <?php Unicamp_THA::instance()->title_bar_heading_after(); ?>
    </span>

    </div>

    <!-- Inline animations -->
    <style>
      /* Typing animation */
      @keyframes typingLoop {
        0%   { max-width: 0ch; }
        10%  { max-width: 0ch; }
        45%  { max-width: calc(var(--chars) * 1ch); }
        65%  { max-width: calc(var(--chars) * 1ch); }
        100% { max-width: 0ch; }
      }
      @keyframes caretBlink {
        50% { border-color: transparent; }
      }

      /* Fully independent floating bubble animations */
      @keyframes bubbleFloat1 {
        0% { transform: translate(0, 0) scale(1); opacity: 0.8; }
        50% { transform: translate(-15px, -25px) scale(1.1); opacity: 0.9; }
        100% { transform: translate(0, 0) scale(1); opacity: 0.8; }
      }
      @keyframes bubbleFloat2 {
        0% { transform: translate(0, 0) scale(1); opacity: 0.7; }
        50% { transform: translate(20px, -15px) scale(1.05); opacity: 0.85; }
        100% { transform: translate(0, 0) scale(1); opacity: 0.7; }
      }
      @keyframes bubbleFloat3 {
        0% { transform: translate(0, 0) scale(1); opacity: 0.9; }
        50% { transform: translate(-10px, 20px) scale(1.08); opacity: 0.85; }
        100% { transform: translate(0, 0) scale(1); opacity: 0.9; }
      }
      @keyframes bubbleFloat4 {
        0% { transform: translate(0, 0) scale(1); opacity: 0.8; }
        50% { transform: translate(15px, -10px) scale(0.95); opacity: 0.9; }
        100% { transform: translate(0, 0) scale(1); opacity: 0.8; }
      }

      @media (prefers-reduced-motion: reduce) {
        span[style*="animation:"] { animation: none !important; }
      }
    </style>
  </div>
</div>
