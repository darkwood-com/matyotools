({
    baseUrl: "../src",
    paths: [
        "matyotools_dev.js"
    ],
    optimize: "none",
    name: "matyotools_dev",
    out: "../src/matyotools_prod.js",
    wrap: {
        startFile: "wrap/start.frag",
        endFile: "wrap/end.frag"
    }
})