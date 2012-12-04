({
    baseUrl: "../src",
    optimize: "none",
    name: "main",
    out: "../matyotools.js",
    wrap: {
        startFile: "wrap/start.frag",
        endFile: "wrap/end.frag"
    }
})