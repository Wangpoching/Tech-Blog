// download-images.js
import fs from 'fs'
import path from 'path'
import https from 'https'
import { createWriteStream } from 'fs'

const UPLOAD_DIR = './uploads/content'
const INPUT_SQL = './seed.sql'
const OUTPUT_SQL = './seed_with_local_images.sql'

// 確保資料夾存在
fs.mkdirSync(UPLOAD_DIR, { recursive: true })

function downloadImage(url, filepath) {
  return new Promise((resolve, reject) => {
    const file = createWriteStream(filepath)
    https.get(url, res => {
      res.pipe(file)
      file.on('finish', () => { file.close(); resolve() })
    }).on('error', err => {
      fs.unlink(filepath, () => {})
      reject(err)
    })
  })
}

async function main() {
  let sql = fs.readFileSync(INPUT_SQL, 'utf-8')
  
  // 找出所有 GitHub 圖片 URL
  const regex = /https:\/\/raw\.githubusercontent\.com\/Wangpoching\/blog-backup\/main\/assets\/images\/([^\s'")\]]+)/g
  const urls = [...new Set([...sql.matchAll(regex)].map(m => m[0]))]
  
  console.log(`找到 ${urls.length} 張圖片`)
  
  for (const [i, url] of urls.entries()) {
    const filename = url.split('/').pop()
    const filepath = path.join(UPLOAD_DIR, filename)
    
    console.log(`[${i+1}/${urls.length}] 下載：${filename}`)
    
    try {
      await downloadImage(url, filepath)
      // 替換 SQL 裡的 URL
      sql = sql.replaceAll(url, `uploads/content/${filename}`)
    } catch (e) {
      console.error(`下載失敗：${filename}`)
    }
    
    await new Promise(r => setTimeout(r, 200))
  }
  
  fs.writeFileSync(OUTPUT_SQL, sql)
  console.log(`\n✅ 完成！輸出到 ${OUTPUT_SQL}`)
}

main().catch(console.error)
