    </div><!-- /.content -->
</div><!-- /.main -->

<script>
// Live clock
function updateClock(){
    const now = new Date();
    const t = now.toLocaleTimeString('en-US',{hour12:false});
    const el = document.getElementById('clock');
    if(el) el.textContent = t;
}
updateClock();
setInterval(updateClock, 1000);
</script>
</body>
</html>
